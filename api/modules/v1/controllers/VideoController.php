<?php

namespace app\modules\v1\controllers;

use app\models\User;
use app\models\Video;
use app\models\VideoFeatured;
use app\models\VideoSearch;
use app\models\VideoStatistics;
use app\modules\v1\repositories\VideoFeaturedRepository;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\ForbiddenHttpException;

/**
 * VideoController implements the CRUD actions for Video model.
 */
class VideoController extends ActiveController
{
    public $modelClass = Video::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'index' => ['get'],
                'view' => ['get'],
                'create' => ['post'],
                'update' => ['put'],
                'delete' => ['delete'],
                'public' => ['get'],
                'featured' => ['get', 'post'],
                'statistics' => ['get'],
                'likes' => ['post'],
            ],
        ];

        return $this->behaviorCors($behaviors);
    }

    protected function behaviorAccess($behaviors)
    {
        // setup access
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['index', 'view', 'create', 'update', 'delete', 'featured', 'featured-update', 'statistics'], //only be applied to
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'view', 'create', 'update', 'delete', 'featured', 'featured-update', 'statistics'],
                    'roles' => ['videoManage'],
                ],
                [
                    'allow' => true,
                    'actions' => ['index', 'view', 'featured', 'likes'],
                    'roles' => ['videoList'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        // Override Delete Action
        unset($actions['delete']);

        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        $actions['view']['findModel'] = [$this, 'findModel'];

        return $actions;
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

        $this->checkAccess('delete', $model, $id);

        $model->status = Video::STATUS_DELETED;

        if ($model->save(false) === false) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }

        $response = Yii::$app->getResponse();
        $response->setStatusCode(204);

        return 'ok';
    }

    public function actionFeatured()
    {
        $params     = Yii::$app->request->getQueryParams();
        $repository = new VideoFeaturedRepository();

        return $repository->getList($params);
    }

    public function actionFeaturedUpdate()
    {
        $params    = Yii::$app->request->getQueryParams();
        $kabkotaId = Arr::get($params, 'kabkota_id');

        $records   = Yii::$app->getRequest()->getBodyParams();

        return $this->parseInputFeatured($kabkotaId, $records);
    }

    protected function parseInputFeatured($kabkotaId, $records)
    {
        $repository = new VideoFeaturedRepository();
        $repository->resetFeatured($kabkotaId);

        foreach ($records as $record) {
            $result = $this->saveFeatured($kabkotaId, $record);

            if ($result !== true) {
                return $result;
            }
        }

        $response = Yii::$app->getResponse();
        $response->setStatusCode(200);

        return $response;
    }

    protected function saveFeatured($kabkotaId, $record)
    {
        if ($kabkotaId !== null) {
            $record['kabkota_id'] = $kabkotaId;
        }

        $model = new VideoFeatured();
        $model->load($record, '');

        if ($model->validate() === false) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(422);

            return $model->getErrors();
        }

        if ($model->save() === false) {
            return $model->getErrors();
        }

        return true;
    }

    public function actionStatistics()
    {
        $params = Yii::$app->request->getQueryParams();
        $statistics = new VideoStatistics();
        return $statistics->getStatistics($params);
    }

    /**
     * Checks the privilege of the current user.
     * throw ForbiddenHttpException if access should be denied
     *
     * @param string $action the ID of the action to be executed
     * @param object $model the model to be accessed. If null, it means no specific model is being accessed.
     * @param array $params additional parameters
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        if ($action === 'update' || $action === 'delete') {
            if ($model->created_by !== \Yii::$app->user->id) {
                throw new ForbiddenHttpException(Yii::t('app', 'error.role.permission'));
            }
        }
    }

    /**
     * @param $id
     * @return mixed|\app\models\Video
     * @throws \yii\web\NotFoundHttpException
     */
    public function findModel($id)
    {
        $model = Video::find()
            ->where(['id' => $id])
            ->andWhere(['!=', 'status', Video::STATUS_DELETED])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException("Object not found: $id");
        }

        $userDetail = User::findIdentity(Yii::$app->user->getId());

        return $model;
    }

    public function prepareDataProvider()
    {
        $params = Yii::$app->request->getQueryParams();

        $authUser = Yii::$app->user;
        $authUserModel = $authUser->identity;

        $authKabKotaId = $authUserModel->kabkota_id;

        $search = new VideoSearch();

        if ($authUser->can('user') || $authUser->can('staffRW')) {
            $search->scenario = VideoSearch::SCENARIO_LIST_USER;
        }

        if ($authUser->can('staffKabkota')) {
            $params['kabkota_id'] = $authKabKotaId;
        }

        return $search->search($params);
    }
}
