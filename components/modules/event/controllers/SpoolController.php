<?php

namespace app\modules\event\controllers;

use app\modules\admin\controllers\AdminController;

/**
 * SpoolController implements actions for Event model.
 */
class SpoolController extends AdminController
{
    public $side = 'event';
	
	public function actions()
    {
        return [
            'index' => [
                'class'	=> 'app\components\actions\Index',
                'search'=> 'app\modules\event\models\EventSpoolSearch',
            ],
            'delete' => [
                'class' => 'app\components\actions\Delete',
                'model' => 'app\modules\event\models\EventSpool'
            ],
            'view' => [
                'class' => 'app\components\actions\View',
                'model' => 'app\modules\event\models\EventSpool'
            ],
            'multipleDelete' => [
                'class' => 'app\components\actions\MultipleDelete',
                'model' => 'app\modules\event\models\EventSpool'
            ],
        ];
    }
}
