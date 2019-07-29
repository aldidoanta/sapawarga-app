<?php

namespace app\modules\v1\controllers;

use app\filters\auth\HttpBearerAuth;
use app\models\User;
use app\models\Video;
use app\models\VideoSearch;
use app\models\VideoLike;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * VideoController implements the CRUD actions for Video model.
 */
class VideoController extends ActiveController
{
    public $modelClass = Video::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBearerAuth::className(),
            ],
        ];

        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'index' => ['get'],
                'view' => ['get'],
                'create' => ['post'],
                'update' => ['put'],
                'delete' => ['delete'],
                'public' => ['get'],
                'statistics' => ['get'],
                'likes'    => ['post'],
            ],
        ];

        return $this->behaviorCors($behaviors);
    }

    protected function behaviorCors($behaviors)
    {
        // remove authentication filter
        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
            ],
        ];

        // re-add authentication filter
        $behaviors['authenticator'] = $auth;
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options', 'public'];

        return $this->behaviorAccess($behaviors);
    }

    protected function behaviorAccess($behaviors)
    {
        // setup access
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['index', 'view', 'create', 'update', 'delete', 'statistics'], //only be applied to
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'view', 'create', 'update', 'delete',  'statistics'],
                    'roles' => ['videoManage'],
                ],
                [
                    'allow' => true,
                    'actions' => ['index', 'view', 'likes'],
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

    public function actionStatistics()
    {
        $params = Yii::$app->request->getQueryParams();
        $statistics = new NewsStatistics();
        return $statistics->getStatistics($params);
    }

    /**
     * Checks the privilege of the current user.
     *
     * This method should be overridden to check whether the current user has the privilege
     * to run the specified action against the specified data model.
     * If the user does not have access, a [[ForbiddenHttpException]] should be thrown.
     *
     * @param string $action the ID of the action to be executed
     * @param object $model the model to be accessed. If null, it means no specific model is being accessed.
     * @param array $params additional parameters
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        //
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

    public function actionLikes($id)
    {
        $userId = Yii::$app->user->getId();
        $user = User::findIdentity($userId);

        /**
         * @var Aspirasi $model
         */
        $model = $this->findModel($id);

        $model = VideoLike::find()
            ->where(['id' => $id])
            ->where(['user_id' => $userId, 'video_id' => $id])
            ->one();

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

    public function prepareDataProvider()
    {
        $params = Yii::$app->request->getQueryParams();

        $user   = Yii::$app->user;

        $search = new VideoSearch();

        if ($user->can('videoManage') === false) {
            $search->scenario = VideoSearch::SCENARIO_LIST_USER;
        }

        return $search->search($params);
    }
}
