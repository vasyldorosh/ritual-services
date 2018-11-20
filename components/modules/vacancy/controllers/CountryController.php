<?php

namespace app\modules\vacancy\controllers;

use app\components\AdminController;

/**
 * CountryController implements actions for Country model.
 */
class CountryController extends AdminController
{
    public function actions()
    {
       $actions = parent::actions() + [
            'index' => [
                'class'	=> 'app\components\actions\Index',
                'search'=> 'app\modules\vacancy\models\Country',
            ],
            'create' => [
                'class' => 'app\components\actions\Create',
                'model' => 'app\modules\vacancy\models\Country',
            ],
            'update' => [
                'class' 		=> 'app\components\actions\Update',
                'model'			=> 'app\modules\vacancy\models\Country',
                'multilingual' 	=> true,
            ],
            'delete' => [
                'class' => 'app\components\actions\Delete',
                'model' => 'app\modules\vacancy\models\Country'
            ],
            'view' => [
                'class' => 'app\components\actions\View',
                'model' => 'app\modules\vacancy\models\Country'
            ],
            'multipleDelete' => [
                'class' => 'app\components\actions\MultipleDelete',
                'model' => 'app\modules\vacancy\models\Country'
            ],
            'multipleActivate' => [
                'class' => 'app\components\actions\MultipleActivate',
                'model' => 'app\modules\vacancy\models\Country'
            ],
            'multipleDeactivate' => [
                'class' => 'app\components\actions\MultipleDeactivate',
                'model' => 'app\modules\vacancy\models\Country'
            ],
        ];
		
		return $actions;
    }
}
