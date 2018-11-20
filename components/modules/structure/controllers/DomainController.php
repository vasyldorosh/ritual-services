<?php

namespace app\modules\structure\controllers;

use app\components\AdminController;

/**
 * DomainController implements actions for Domain model.
 */
class DomainController extends AdminController
{
	public $side = 'module';
	
    public function actions()
    {
       $actions = parent::actions() + [
            'index' => [
                'class'	=> 'app\components\actions\Index',
                'search'=> 'app\modules\structure\models\DomainSearch',
            ],
            'create' => [
                'class' => 'app\components\actions\Create',
                'model' => 'app\modules\structure\models\Domain',
            ],
            'update' => [
                'class'	=> 'app\components\actions\Update',
                'model'	=> 'app\modules\structure\models\Domain',
				'multilingual' 	=> true,
            ],
            'delete' => [
                'class' => 'app\components\actions\Delete',
                'model' => 'app\modules\structure\models\Domain'
            ],
            'view' => [
                'class' => 'app\components\actions\View',
                'model' => 'app\modules\structure\models\Domain'
            ],
            'multipleDelete' => [
                'class' => 'app\components\actions\MultipleDelete',
                'model' => 'app\modules\structure\models\Domain'
            ],
            'multipleActivate' => [
                'class' => 'app\components\actions\MultipleActivate',
                'model' => 'app\modules\structure\models\Domain'
            ],
            'multipleDeactivate' => [
                'class' => 'app\components\actions\MultipleDeactivate',
                'model' => 'app\modules\structure\models\Domain'
            ],
            'excel' => [
                'class'	=> 'app\components\actions\ExportExcel',
                'model'=> 'app\modules\structure\models\Domain',
            ],			
            'csv' => [
                'class'	=> 'app\components\actions\ExportCsv',
                'model'=> 'app\modules\structure\models\Domain',
            ],			
        ];
		
		return $actions;
    }
}
