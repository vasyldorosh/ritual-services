<?php

namespace app\modules\menu\controllers;

use app\components\AdminController;

/**
 * EventController implements actions for Event model.
 */
class MenuController extends AdminController
{
	public $side = '';
	
    public function actions()
    {
       $actions = parent::actions() + [
            'index' => [
                'class'	=> 'app\components\actions\Index',
                'search'=> 'app\modules\menu\models\MenuSearch',
            ],
            'create' => [
                'class' => 'app\components\actions\Create',
                'model' => 'app\modules\menu\models\Menu',
            ],
            'update' => [
                'class' 	=> 'app\components\actions\Update',
                'model'		=> 'app\modules\menu\models\Menu',
            ],
            'delete' => [
                'class' => 'app\components\actions\Delete',
                'model' => 'app\modules\menu\models\Menu'
            ],
            'view' => [
                'class' => 'app\components\actions\View',
                'model' => 'app\modules\menu\models\Menu'
            ],
            'multipleDelete' => [
                'class' => 'app\components\actions\MultipleDelete',
                'model' => 'app\modules\menu\models\Menu'
            ],
            'multipleActivate' => [
                'class' => 'app\components\actions\MultipleActivate',
                'model' => 'app\modules\menu\models\Menu'
            ],
            'multipleDeactivate' => [
                'class' => 'app\components\actions\MultipleDeactivate',
                'model' => 'app\modules\menu\models\Menu'
            ],
        ];
		
		return $actions;
    }
}
