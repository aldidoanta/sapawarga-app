<?php

namespace app\modules\v1\controllers;

use app\models\Category;
use app\models\CategorySearch;
use app\models\NewsHoax;
use app\models\Notification;
use Illuminate\Support\Arr;
use Yii;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends ActiveController
{
    public $modelClass = Category::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbs'] = [
            'class'   => \yii\filters\VerbFilter::className(),
            'actions' => [
                'index'  => ['get'],
                'view'   => ['get'],
                'create' => ['post'],
                'update' => ['put'],
                'delete' => ['delete'],
                'public' => ['get'],
                'types'  => ['get'],
            ],
        ];

        return $this->behaviorCors($behaviors);
    }

    protected function behaviorAccess($behaviors)
    {
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only'  => ['index', 'view', 'create', 'update', 'delete', 'types'], //only be applied to
            'rules' => [
                [
                    'allow'   => true,
                    'actions' => ['index', 'view', 'create', 'update', 'delete', 'types'],
                    'roles'   => ['admin', 'manageUsers'],
                ],
                [
                    'allow'   => true,
                    'actions' => ['index', 'view'],
                    'roles'   => ['user', 'staffRW', 'newsSaberhoaxManage'],
                ],
                [
                    'allow'   => true,
                    'actions' => ['types'],
                    'roles'   => ['newsSaberhoaxManage'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        // Override delete action
        unset($actions['delete']);

        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        $actions['view']['findModel']            = [$this, 'findModel'];

        return $actions;
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $model->status = Category::STATUS_DELETED;

        if ($model->save(false) === false) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }

        $response = Yii::$app->getResponse();
        $response->setStatusCode(204);

        return 'ok';
    }

    public function actionTypes()
    {
        $model = Category::find()
            ->select('type as id')
            ->groupBy('type')
            ->asArray()
            ->all();

        $user = Yii::$app->user;
        if ($user->can('newsSaberhoaxManage')) {
            // Hanya menampilkan tipe kategori 'newsHoax'
            $index = array_search(['id' => NewsHoax::CATEGORY_TYPE], $model);
            $model = [$model[$index]];
        } elseif ($user->can('admin')) {
            // Tidak menampilkan tipe kategori 'notification'
            $model = array_filter($model, function ($categoryType) {
                return $categoryType['id'] !== Notification::CATEGORY_TYPE;
            });
        } else {
            // Tidak menampilkan tipe kategori 'newsHoax' dan 'notification'
            $model = array_filter($model, function ($categoryType) {
                return !(in_array($categoryType['id'], Category::EXCLUDED_TYPES));
            });
        }

        foreach ($model as &$type) {
            $type['name'] = Category::TYPE_MAP[$type['id']];
        }

        $name = array_column($model, 'name');
        array_multisort($name, SORT_ASC, $model);

        $response = Yii::$app->getResponse();
        $response->setStatusCode(200);
        return [ 'items' => $model ];
    }

    /**
     * @param $id
     * @return mixed|Category
     * @throws \yii\web\NotFoundHttpException
     */
    public function findModel($id)
    {
        $model = Category::find()
            ->where(['id' => $id])
            ->andWhere(['!=', 'status', Category::STATUS_DELETED])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException("Object not found: $id");
        }

        return $model;
    }

    public function prepareDataProvider()
    {
        $search = new CategorySearch();
        $user   = Yii::$app->user;

        if ($user->can('admin')) {
            $search->scenario = CategorySearch::SCENARIO_LIST_ADMIN;
        } elseif ($user->can('newsSaberhoaxManage')) {
            $search->scenario = CategorySearch::SCENARIO_LIST_SABERHOAX;
        } else {
            $search->scenario = CategorySearch::SCENARIO_LIST_OTHER_ROLE;
        }

        return $search->search(\Yii::$app->request->getQueryParams());
    }
}
