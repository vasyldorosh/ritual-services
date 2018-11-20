<?php

namespace app\modules\redirect\controllers;

use app\components\AdminController;

/**
 * RedirectController implements actions for Event model.
 */
class RedirectController extends AdminController
{
	public $side = 'module';
	
    public function actions()
    {
       $actions = parent::actions() + [
            'index' => [
                'class'	=> 'app\components\actions\Index',
                'search'=> 'app\modules\redirect\models\RedirectSearch',
            ],
            'create' => [
                'class' => 'app\components\actions\Create',
                'model' => 'app\modules\redirect\models\Redirect',
            ],
            'update' => [
                'class' 	=> 'app\components\actions\Update',
                'model'		=> 'app\modules\redirect\models\Redirect',
            ],
            'delete' => [
                'class' => 'app\components\actions\Delete',
                'model' => 'app\modules\redirect\models\Redirect'
            ],
            'view' => [
                'class' => 'app\components\actions\View',
                'model' => 'app\modules\redirect\models\Redirect'
            ],
            'multipleDelete' => [
                'class' => 'app\components\actions\MultipleDelete',
                'model' => 'app\modules\redirect\models\Redirect'
            ],
            'multipleActivate' => [
                'class' => 'app\components\actions\MultipleActivate',
                'model' => 'app\modules\redirect\models\Redirect'
            ],
            'multipleDeactivate' => [
                'class' => 'app\components\actions\MultipleDeactivate',
                'model' => 'app\modules\redirect\models\Redirect'
            ],
        ];
		
		return $actions;
    }
}
