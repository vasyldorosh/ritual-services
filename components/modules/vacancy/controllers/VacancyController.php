<?php

namespace app\modules\vacancy\controllers;

use app\components\AdminController;

/**
 * VacancyController implements actions for Vacancy model.
 */
class VacancyController extends AdminController
{
    public function actions()
    {
       $actions = parent::actions() + [
            'index' => [
                'class'	=> 'app\components\actions\Index',
                'search'=> 'app\modules\vacancy\models\Vacancy',
            ],
            'create' => [
                'class' => 'app\components\actions\Create',
                'model' => 'app\modules\vacancy\models\Vacancy',
            ],
            'update' => [
                'class' 		=> 'app\components\actions\Update',
                'model'			=> 'app\modules\vacancy\models\Vacancy',
                'multilingual' 	=> true,
            ],
            'delete' => [
                'class' => 'app\components\actions\Delete',
                'model' => 'app\modules\vacancy\models\Vacancy'
            ],
            'view' => [
                'class' => 'app\components\actions\View',
                'model' => 'app\modules\vacancy\models\Vacancy'
            ],
            'multipleDelete' => [
                'class' => 'app\components\actions\MultipleDelete',
                'model' => 'app\modules\vacancy\models\Vacancy'
            ],
            'multipleActivate' => [
                'class' => 'app\components\actions\MultipleActivate',
                'model' => 'app\modules\vacancy\models\Vacancy'
            ],
            'multipleDeactivate' => [
                'class' => 'app\components\actions\MultipleDeactivate',
                'model' => 'app\modules\vacancy\models\Vacancy'
            ],
        ];
		
		return $actions;
    }
}
