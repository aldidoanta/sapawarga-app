<?php

namespace app\modules\v1\controllers;

use app\components\CustomCors;
use app\filters\auth\HttpBearerAuth;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\ServerErrorHttpException;

/**
 * NewsController implements the CRUD actions for News model.
 */
class NewsJabarController extends Controller
{
    const URL_NEWS = 'http://humas.jabarprov.go.id/api/berita-terkini';

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
                'public' => ['get'],
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
            'class' => CustomCors::className(),
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
            'only' => ['index'],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index'],
                    'roles'   => ['admin', 'manageStaffs', 'staffRW', 'user'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actionOptions ()
    {
        if (Yii::$app->getRequest()->getMethod() !== 'OPTIONS') {
            Yii::$app->getResponse()->setStatusCode(405);
        }
        $options = $this->_verbs;
        Yii::$app->getResponse()->getHeaders()->set('Allow', implode(', ', $options));
    }

    /**
     * Delete entity with soft delete / status flagging
     *
     * @return string
     * @throws \yii\web\ServerErrorHttpException
     */
    public function actionIndex()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::URL_NEWS);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $curlResponse = curl_exec($curl);

        if ($curlResponse === false) {
            $info = curl_getinfo($curl);
            curl_close($curl);
            throw new ServerErrorHttpException(var_export($info));
        }

        curl_close($curl);

        $response = Yii::$app->getResponse();
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = [
            'items' => $this->parseNewsResponse($curlResponse)
        ];
        $response->setStatusCode(200);

        return $response;
    }

    protected function parseNewsResponse($rawResponse)
    {
        $func = function ($value) {
            return [
                'id'            => $value['ID'],
                'title'         => $value['post_title'],
                'thumbnail'     => $value['thumbnail'],
                'url'           => $value['slug'],
                'created_at'    => strtotime($value['tgl_publish']),
                'updated_at'    => strtotime($value['tgl_publish']),
            ];
        };

        $arrayResponse = json_decode($rawResponse, JSON_OBJECT_AS_ARRAY);

        $result = array_map($func, $arrayResponse);
        return $result;
    }
}
