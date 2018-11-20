<?php

namespace app\modules\slider\controllers;

use app\components\AdminController;

/**
 * SlideController implements actions for Slide model.
 */
class SlideController extends AdminController
{
    public function actions()
    {
       $actions = parent::actions() + [
            'index' => [
                'class'	=> 'app\components\actions\Index',
                'search'=> 'app\modules\slider\models\Slide',
            ],
            'create' => [
                'class' => 'app\components\actions\Create',
                'model' => 'app\modules\slider\models\Slide',
            ],
            'update' => [
                'class' 		=> 'app\components\actions\Update',
                'model'			=> 'app\modules\slider\models\Slide',
                'multilingual' 	=> true,
            ],
            'delete' => [
                'class' => 'app\components\actions\Delete',
                'model' => 'app\modules\slider\models\Slide'
            ],
            'view' => [
                'class' => 'app\components\actions\View',
                'model' => 'app\modules\slider\models\Slide'
            ],
            'multipleDelete' => [
                'class' => 'app\components\actions\MultipleDelete',
                'model' => 'app\modules\slider\models\Slide'
            ],
            'multipleActivate' => [
                'class' => 'app\components\actions\MultipleActivate',
                'model' => 'app\modules\slider\models\Slide'
            ],
            'multipleDeactivate' => [
                'class' => 'app\components\actions\MultipleDeactivate',
                'model' => 'app\modules\slider\models\Slide'
            ],
        ];
		
		return $actions;
    }
}
