<?php

namespace app\modules\event\controllers;

use app\components\AdminController;

/**
 * EventController implements actions for Event model.
 */
class EventController extends AdminController
{
	public $side = 'event';
	
    public function actions()
    {
       $actions = parent::actions() + [
            'index' => [
                'class'	=> 'app\components\actions\Index',
                'search'=> 'app\modules\event\models\EventSearch',
            ],
            'create' => [
                'class' => 'app\components\actions\Create',
                'model' => 'app\modules\event\models\Event',
            ],
            'update' => [
                'class' 		=> 'app\components\actions\Update',
                'model'			=> 'app\modules\event\models\Event',
                'multilingual' 	=> true,
            ],
            'delete' => [
                'class' => 'app\components\actions\Delete',
                'model' => 'app\modules\event\models\Event'
            ],
            'view' => [
                'class' => 'app\components\actions\View',
                'model' => 'app\modules\event\models\Event'
            ],
            'multipleDelete' => [
                'class' => 'app\components\actions\MultipleDelete',
                'model' => 'app\modules\event\models\Event'
            ],
            'multipleActivate' => [
                'class' => 'app\components\actions\MultipleActivate',
                'model' => 'app\modules\event\models\Event'
            ],
            'multipleDeactivate' => [
                'class' => 'app\components\actions\MultipleDeactivate',
                'model' => 'app\modules\event\models\Event'
            ],
        ];
		
		//d($actions);
		
		return $actions;
    }
}
