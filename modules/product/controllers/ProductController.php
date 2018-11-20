<?php

namespace app\modules\product\controllers;

use app\components\AdminController;

/**
 * ProductController implements actions for Product model.
 */
class ProductController extends AdminController
{
    public function actions()
    {
       $actions = parent::actions() + [
            'index' => [
                'class'	=> 'app\components\actions\Index',
                'search'=> 'app\modules\product\models\Product',
            ],
            'create' => [
                'class' => 'app\components\actions\Create',
                'model' => 'app\modules\product\models\Product',
            ],
            'update' => [
                'class' 		=> 'app\components\actions\Update',
                'model'			=> 'app\modules\product\models\Product',
                'multilingual' 	=> true,
            ],
            'delete' => [
                'class' => 'app\components\actions\Delete',
                'model' => 'app\modules\product\models\Product'
            ],
            'view' => [
                'class' => 'app\components\actions\View',
                'model' => 'app\modules\product\models\Product'
            ],
            'multipleDelete' => [
                'class' => 'app\components\actions\MultipleDelete',
                'model' => 'app\modules\product\models\Product'
            ],
            'multipleActivate' => [
                'class' => 'app\components\actions\MultipleActivate',
                'model' => 'app\modules\product\models\Product'
            ],
            'multipleDeactivate' => [
                'class' => 'app\components\actions\MultipleDeactivate',
                'model' => 'app\modules\product\models\Product'
            ],
        ];
		
		return $actions;
    }
}
