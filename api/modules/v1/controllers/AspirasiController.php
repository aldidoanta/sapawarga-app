<?php

namespace app\modules\v1\controllers;

use app\models\Aspirasi;
use app\models\AspirasiSearch;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * AspirasiController implements the CRUD actions for Aspirasi model.
 */
class AspirasiController extends ActiveController
{
    public $modelClass = Aspirasi::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbs'] = [
            'class'   => \yii\filters\VerbFilter::className(),
            'actions' => [
                'index'    => ['get'],
                'view'     => ['get'],
                'create'   => ['post'],
                'update'   => ['put'],
                'delete'   => ['delete'],
                'public'   => ['get'],
                'approval' => ['post'],
                'likes'    => ['post'],
                'me'       => ['get'],
            ],
        ];

        return $this->behaviorCors($behaviors);
    }

    protected function behaviorAccess($behaviors)
    {
        // setup access
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only'  => ['index', 'view', 'create', 'update', 'delete', 'approval', 'likes', 'me'],
            'rules' => \array_merge($this->getAdminAndCRUDRules(), $this->getExtraPatternsRules()),
        ];

        return $behaviors;
    }

    protected function getAdminAndCRUDRules()
    {
        return [
            [
                'allow'   => true,
                'roles'   => ['admin'],
            ],
            [
                'allow'   => true,
                'actions' => ['index', 'view'],
                'roles'   => [
                    'viewAllAspirasi',
                    'viewAddressedCascadedAspirasi',
                    'viewPublishedAspirasi'
                ],
            ],
            [
                'allow'   => true,
                'actions' => ['create'],
                'roles'   => ['createAspirasi'],
            ],
            [
                'allow'      => true,
                'actions'    => ['update'],
                'roles'      => ['editOwnAspirasi'],
                'roleParams' => function ($rule) {
                    return ['aspirasi' => $this->findModel(Yii::$app->request->get('id'))];
                },
            ],
            [
                'allow'   => true,
                'actions' => ['delete'],
                'roles'   => ['deleteOwnAspirasi'],
                'roleParams' => function ($rule) {
                    return ['aspirasi' => $this->findModel(Yii::$app->request->get('id'))];
                },
            ],
        ];
    }

    protected function getExtraPatternsRules()
    {
        return [
            [
                'allow'      => true,
                'actions'    => ['approval'],
                'roles'      => ['approvalAspirasi'],
                'roleParams' => function ($rule) {
                    return ['aspirasi' => $this->findModel(Yii::$app->request->get('id'))];
                },
            ],
            [
                'allow'      => true,
                'actions'    => ['likes'],
                'roles'      => ['likeAspirasi'],
                'roleParams' => function ($rule) {
                    return ['aspirasi' => $this->findModel(Yii::$app->request->get('id'))];
                },
            ],
            [
                'allow'      => true,
                'actions'    => ['me', 'view'],
                'roles'      => ['viewOwnAspirasi'],
            ],
        ];
    }

    public function actions()
    {
        $actions = parent::actions();

        // Override Delete Action
        unset($actions['delete']);
        unset($actions['create']);
        unset($actions['update']);

        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        $actions['view']['findModel']            = [$this, 'findModel'];

        return $actions;
    }

    public function actionCreate()
    {
        $model            = new Aspirasi(['scenario' => Aspirasi::SCENARIO_USER_CREATE]);
        $model->author_id = Yii::$app->user->getId();

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->validate() && $model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
        } else {
            // Validation error
            $response = Yii::$app->getResponse();
            $response->setStatusCode(422);

            return $model->getErrors();
        }

        return $model;
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->validate() && $model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(200);
        } else {
            // Validation error
            $response = Yii::$app->getResponse();
            $response->setStatusCode(422);

            return $model->getErrors();
        }

        return $model;
    }

    /**
     * Delete entity with soft delete / status flagging
     *
     * @param $id
     * @return string
     * @throws \yii\web\ForbiddenHttpException
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\ServerErrorHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        return $this->applySoftDelete($model);
    }

    public function actionApproval($id)
    {
        $model = $this->findModel($id);

        return $this->processApproval($model);
    }

    protected function processApproval($model)
    {
        $action = Yii::$app->request->post('action');
        $note   = Yii::$app->request->post('note');

        $currentUserId = Yii::$app->user->getId();

        if ($action === Aspirasi::ACTION_APPROVE) {
            $model->status = Aspirasi::STATUS_PUBLISHED;
        } elseif ($action === Aspirasi::ACTION_REJECT) {
            $model->status = Aspirasi::STATUS_APPROVAL_REJECTED;
        } else {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(400);
            return 'Bad Request: Invalid Action';
        }

        $model->approval_note = $note;
        $model->approved_by   = $currentUserId;
        $model->touch('approved_at');

        if ($model->save(false) === false) {
            throw new ServerErrorHttpException('Failed to process the object for unknown reason.');
        }

        $response = Yii::$app->getResponse();
        $response->setStatusCode(200);

        return 'ok';
    }

    public function actionLikes($id)
    {
        $userId = Yii::$app->user->getId();
        $user   = User::findIdentity($userId);

        /**
         * @var Aspirasi $model
         */
        $model = $this->findModel($id);

        $count = (new \yii\db\Query())
            ->from('aspirasi_likes')
            ->where(['user_id' => $userId, 'aspirasi_id' => $id])
            ->count();

        $alreadyLiked = (int) $count > 0;

        if ($alreadyLiked > 0) {
            $model->unlink('likes', $user, true);
        } else {
            $model->link('likes', $user);
        }

        $response = Yii::$app->getResponse();
        $response->setStatusCode(200);

        return 'ok';
    }

    public function actionMe()
    {
        $userId = Yii::$app->user->getId();
        $user   = User::findIdentity($userId);

        $search            = new AspirasiSearch();
        $search->author_id = $userId;
        $search->user      = $user;

        $params = Yii::$app->request->getQueryParams();
        return $search->search($params, true);
    }

    /**
     * @param $id
     * @return mixed|Aspirasi
     * @throws \yii\web\NotFoundHttpException
     */
    public function findModel($id)
    {
        $model = Aspirasi::find()
            ->where(['id' => $id])
            ->andWhere(['!=', 'status', Aspirasi::STATUS_DELETED])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException("Object not found: $id");
        }

        return $model;
    }
}
