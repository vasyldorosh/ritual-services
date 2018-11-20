<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\helpers\ArrayHelper;

class GroupController extends \app\components\AdminController
{
	public $side = 'admin';
	
    public function actions()
    {
         $actions = parent::actions() + [
            'index' => [
                'class'	=> 'app\components\actions\Index',
                'search'=> 'app\modules\admin\models\GroupSearch',
            ],
            'create' => [
                'class' => 'app\components\actions\Create',
                'model' => 'app\modules\admin\models\Group',
            ],
            'update' => [
                'class' => 'app\components\actions\Update',
                'model'	=> 'app\modules\admin\models\Group',
            ],
            'delete' => [
                'class' => 'app\components\actions\Delete',
                'model' => 'app\modules\admin\models\Group'
            ],
            'view' => [
                'class' => 'app\components\actions\View',
                'model' => 'app\modules\admin\models\Group'
            ],
            'multipleDelete' => [
                'class' => 'app\components\actions\MultipleDelete',
                'model' => 'app\modules\admin\models\Group'
            ],
            'multipleActivate' => [
                'class' => 'app\components\actions\MultipleActivate',
                'model' => 'app\modules\admin\models\Group'
            ],
            'multipleDeactivate' => [
                'class' => 'app\components\actions\MultipleDeactivate',
                'model' => 'app\modules\admin\models\Group'
            ],			
        ];
		
		return $actions;
    }

}