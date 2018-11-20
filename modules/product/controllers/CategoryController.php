<?php

namespace app\modules\product\controllers;

use app\components\AdminController;

/**
 * CategoryController implements actions for Category model.
 */
class CategoryController extends AdminController
{
    public function actions()
    {
       $actions = parent::actions() + [
            'index' => [
                'class'	=> 'app\components\actions\Index',
                'search'=> 'app\modules\product\models\Category',
            ],
            'create' => [
                'class' => 'app\components\actions\Create',
                'model' => 'app\modules\product\models\Category',
            ],
            'update' => [
                'class' 		=> 'app\components\actions\Update',
                'model'			=> 'app\modules\product\models\Category',
                'multilingual' 	=> true,
            ],
            'delete' => [
                'class' => 'app\components\actions\Delete',
                'model' => 'app\modules\product\models\Category'
            ],
            'view' => [
                'class' => 'app\components\actions\View',
                'model' => 'app\modules\product\models\Category'
            ],
            'multipleDelete' => [
                'class' => 'app\components\actions\MultipleDelete',
                'model' => 'app\modules\product\models\Category'
            ],
            'multipleActivate' => [
                'class' => 'app\components\actions\MultipleActivate',
                'model' => 'app\modules\product\models\Category'
            ],
            'multipleDeactivate' => [
                'class' => 'app\components\actions\MultipleDeactivate',
                'model' => 'app\modules\product\models\Category'
            ],
        ];
		
		return $actions;
    }
}
