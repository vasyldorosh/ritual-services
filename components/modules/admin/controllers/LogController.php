<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\helpers\ArrayHelper;

class LogController extends \app\components\AdminController
{
	public $side = 'admin';
	
    public function actions()
    {
         $actions = parent::actions() + [
            'index' => [
                'class'	=> 'app\components\actions\Index',
                'search'=> 'app\modules\admin\models\Log',
            ],			
        ];
		
		return $actions;
    }	
}