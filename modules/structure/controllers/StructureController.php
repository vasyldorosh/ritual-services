<?php

namespace app\modules\structure\controllers;

use app\modules\structure\models\Page;
use app\components\AdminController;

/**
 * StructureController implements actions for Structure model.
 */
class StructureController extends AdminController
{
    public function _actions()
    {
       $actions = parent::actions() + [
            'index' => [
                'class'	=> 'app\components\actions\Index',
                'search'=> 'app\modules\event\models\StructureSearch',
            ],
            'create' => [
                'class' => 'app\components\actions\Create',
                'model' => 'app\modules\event\models\Structure',
            ],
            'update' => [
                'class' 		=> 'app\components\actions\Update',
                'model'			=> 'app\modules\event\models\Structure',
                'multilingual' 	=> true,
            ],
            'delete' => [
                'class' => 'app\components\actions\Delete',
                'model' => 'app\modules\event\models\Structure'
            ],
            'view' => [
                'class' => 'app\components\actions\View',
                'model' => 'app\modules\event\models\Structure'
            ],
            'multipleDelete' => [
                'class' => 'app\components\actions\MultipleDelete',
                'model' => 'app\modules\event\models\Structure'
            ],
            'multipleActivate' => [
                'class' => 'app\components\actions\MultipleActivate',
                'model' => 'app\modules\event\models\Structure'
            ],
            'multipleDeactivate' => [
                'class' => 'app\components\actions\MultipleDeactivate',
                'model' => 'app\modules\event\models\Structure'
            ],
        ];
		
		//d($actions);
		
		return $actions;
    }
	
	public function init() 
	{
		$this->layout = '@app/views/layouts/admin/structure';	
		
		return parent::init();
	}
	
	public function actionIndex() 
	{
		return $this->render('index');
	}
	
}
