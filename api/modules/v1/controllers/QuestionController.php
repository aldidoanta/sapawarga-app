<?php

namespace app\modules\v1\controllers;

use app\models\Question;
use yii\filters\AccessControl;

class QuestionController extends ActiveController
{
    public $modelClass = Question::class;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        return $this->behaviorCors($behaviors);
    }

    protected function behaviorAccess($behaviors)
    {
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'only'  => ['index', 'view', 'create'],
            'rules' => [
                [
                    'allow'   => true,
                    'actions' => ['index', 'view', 'create'],
                    'roles'   => ['admin'],
                ],
            ],
        ];

        return $behaviors;
    }
}
