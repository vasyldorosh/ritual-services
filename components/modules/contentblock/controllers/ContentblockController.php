<?php

namespace app\modules\contentblock\controllers;

use app\components\AdminController;

/**
 * Contentblock Controller implements actions for ContentBlock model.
 */
class ContentblockController extends AdminController
{
    public function actions()
    {
       $actions = parent::actions() + [
            'index' => [
                'class'	=> 'app\components\actions\Index',
                'search'=> 'app\modules\contentblock\models\ContentBlockSearch',
            ],
            'create' => [
                'class' => 'app\components\actions\Create',
                'model' => 'app\modules\contentblock\models\ContentBlock',
            ],
            'update' => [
                'class' 		=> 'app\components\actions\Update',
                'model'			=> 'app\modules\contentblock\models\ContentBlock',
                'multilingual' 	=> true,
            ],
            'delete' => [
                'class' => 'app\components\actions\Delete',
                'model' => 'app\modules\contentblock\models\ContentBlock'
            ],
            'view' => [
                'class' => 'app\components\actions\View',
                'model' => 'app\modules\contentblock\models\ContentBlock'
            ],
            'multipleDelete' => [
                'class' => 'app\components\actions\MultipleDelete',
                'model' => 'app\modules\contentblock\models\ContentBlock'
            ],
            'multipleActivate' => [
                'class' => 'app\components\actions\MultipleActivate',
                'model' => 'app\modules\contentblock\models\ContentBlock'
            ],
            'multipleDeactivate' => [
                'class' => 'app\components\actions\MultipleDeactivate',
                'model' => 'app\modules\contentblock\models\ContentBlock'
            ],
        ];
		
		return $actions;
    }
}
