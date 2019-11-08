<?php

namespace app\modules\v1\controllers;

use app\models\NewsHoaxType;
use yii\filters\AccessControl;

/**
 * NewsHoaxTypeController implements the CRUD actions for NewsHoaxType model.
 */
class NewsHoaxTypeController extends ActiveController
{
    public $modelClass = NewsHoaxType::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        return $this->behaviorCors($behaviors);
    }

    protected function behaviorAccess($behaviors)
    {
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'only'  => ['index', 'view', 'create', 'update', 'delete'],
            'rules' => [
                [
                    'allow'   => true,
                    'actions' => ['index'],
                    'roles'   => ['@'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index']);

        return $actions;
    }

    public function actionIndex()
    {
        $hoaxTypes = include __DIR__ . '/../../../config/references/news_hoax_types.php';
        $title = array_column($hoaxTypes, 'title');
        array_multisort($title, SORT_ASC, $hoaxTypes);
        return [
            'items' => $hoaxTypes,
        ];
    }
}
