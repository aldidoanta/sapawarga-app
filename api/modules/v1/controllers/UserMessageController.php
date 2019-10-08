<?php

namespace app\modules\v1\controllers;

use app\filters\auth\HttpBearerAuth;
use app\models\User;
use app\models\UserMessage;
use app\models\UserMessageSearch;
use Jdsteam\Sapawarga\Filters\RecordLastActivity;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use Hashids\Hashids;

/**
 * MessageController implements the CRUD actions for User Message model.
 */
class UserMessageController extends ActiveController
{
    public $modelClass = UserMessage::class;

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
            ],
        ];

        $behaviors['recordLastActivity'] = [
            'class' => RecordLastActivity::class,
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
                'Access-Control-Request-Method' => ['GET'],
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
            'only' => ['index', 'view'], //only be applied to
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'view'],
                    'roles' => ['userMessageList'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        $actions['view']['findModel'] = [$this, 'findModel'];

        return $actions;
    }

    /**
     * @param $id
     * @return mixed|\app\models\UserMessage
     * @throws \yii\web\NotFoundHttpException
     */
    public function findModel($id)
    {
        $hashids = new Hashids(\Yii::$app->params['hashidSaltSecret'], \Yii::$app->params['hashidLengthPad']);
        $idDecode = $hashids->decode($id);

        if (empty($idDecode)) {
            throw new NotFoundHttpException("Object not found: $id");
        }

        $userDetail = User::findIdentity(Yii::$app->user->getId());

        $model = UserMessage::find()
            ->where(['id' => $idDecode[0]])
            ->andWhere(['<>', 'status', UserMessage::STATUS_DELETED])
            ->andWhere(['=', 'recipient_id', $userDetail->id])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException("Object not found: $id");
        }

        // Update time read_at
        $model->touch('read_at');
        $model->save(false);

        return $model;
    }

    public function prepareDataProvider()
    {
        $params = Yii::$app->request->getQueryParams();

        $authUser = Yii::$app->user;
        $authUserModel = $authUser->identity;
        $params['user_id'] = $authUserModel->id;

        $search = new UserMessageSearch();

        return $search->search($params);
    }
}
