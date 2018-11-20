<?php

namespace app\modules\slider\controllers;

use app\components\AdminController;

/**
 * SliderController implements actions for Slider model.
 */
class SliderController extends AdminController
{
    public function actions()
    {
       $actions = parent::actions() + [
            'index' => [
                'class'	=> 'app\components\actions\Index',
                'search'=> 'app\modules\slider\models\Slider',
            ],
            'create' => [
                'class' => 'app\components\actions\Create',
                'model' => 'app\modules\slider\models\Slider',
            ],
            'update' => [
                'class' 		=> 'app\components\actions\Update',
                'model'			=> 'app\modules\slider\models\Slider',
                'multilingual' 	=> true,
            ],
            'delete' => [
                'class' => 'app\components\actions\Delete',
                'model' => 'app\modules\slider\models\Slider'
            ],
            'view' => [
                'class' => 'app\components\actions\View',
                'model' => 'app\modules\slider\models\Slider'
            ],
            'multipleDelete' => [
                'class' => 'app\components\actions\MultipleDelete',
                'model' => 'app\modules\slider\models\Slider'
            ],
            'multipleActivate' => [
                'class' => 'app\components\actions\MultipleActivate',
                'model' => 'app\modules\slider\models\Slider'
            ],
            'multipleDeactivate' => [
                'class' => 'app\components\actions\MultipleDeactivate',
                'model' => 'app\modules\slider\models\Slider'
            ],
        ];
		
		return $actions;
    }
}
