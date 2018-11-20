<?php

namespace app\modules\structure\controllers;

use app\components\AdminController;

/**
 * AliasController implements actions for Alias model.
 */
class AliasController extends AdminController
{
	public $side = 'module';
	
    public function actions()
    {
       $actions = parent::actions() + [
            'index' => [
                'class'	=> 'app\components\actions\Index',
                'search'=> 'app\modules\structure\models\AliasSearch',
            ],
            'create' => [
                'class' => 'app\components\actions\Create',
                'model' => 'app\modules\structure\models\Alias',
            ],
            'update' => [
                'class'	=> 'app\components\actions\Update',
                'model'	=> 'app\modules\structure\models\Alias',
            ],
            'delete' => [
                'class' => 'app\components\actions\Delete',
                'model' => 'app\modules\structure\models\Alias'
            ],
            'view' => [
                'class' => 'app\components\actions\View',
                'model' => 'app\modules\structure\models\Alias'
            ],
            'multipleDelete' => [
                'class' => 'app\components\actions\MultipleDelete',
                'model' => 'app\modules\structure\models\Alias'
            ],
            'multipleActivate' => [
                'class' => 'app\components\actions\MultipleActivate',
                'model' => 'app\modules\structure\models\Alias'
            ],
            'multipleDeactivate' => [
                'class' => 'app\components\actions\MultipleDeactivate',
                'model' => 'app\modules\structure\models\Alias'
            ],
        ];
		
		return $actions;
    }
}
