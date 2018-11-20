<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use app\modules\admin\models\Admin;

class AdminController extends \app\components\AdminController
{
	public $side = 'admin';
	
	public static function getExceptActions()
	{
		return [
			'login',
			'accessDenied',
		];
	}	
	
    public function actions()
    {
         $actions = parent::actions() + [
            'index' => [
                'class'	=> 'app\components\actions\Index',
                'search'=> 'app\modules\admin\models\AdminSearch',
            ],
            'create' => [
                'class' => 'app\components\actions\Create',
                'model' => 'app\modules\admin\models\Admin',
            ],
            'update' => [
                'class'		=> 'app\components\actions\Update',
				'model' 	=> 'app\modules\admin\models\Admin',
                'params'	=> ['updateRelated'=>true],
            ],
            'delete' => [
                'class' => 'app\components\actions\Delete',
                'model' => 'app\modules\admin\models\Admin'
            ],
            'view' => [
                'class' => 'app\components\actions\View',
                'model' => 'app\modules\admin\models\Admin'
            ],
            'multipleDelete' => [
                'class' => 'app\components\actions\MultipleDelete',
                'model' => 'app\modules\admin\models\Admin'
            ],
            'multipleActivate' => [
                'class' => 'app\components\actions\MultipleActivate',
                'model' => 'app\modules\admin\models\Admin'
            ],
            'multipleDeactivate' => [
                'class' => 'app\components\actions\MultipleDeactivate',
                'model' => 'app\modules\admin\models\Admin'
            ],
            'eventLog' => [
                'class'	=> 'app\components\actions\Index',
                'search'=> 'app\modules\admin\models\AdminVsEventLog',
                'view'=> 'eventLog',
            ],			
        ];
		
		return $actions;
    }	

    public function actionLogin($id, $root=0)
    {
		if (is_super_admin() || (!is_super_admin() && !empty(session()->get("root_id")))) {
			$admin = Admin::findOne($id);
			
			if (!empty($admin)) {
				$loggedId = admin()->id;
				admin()->logout();
				
				if (!$root) {
					session()->set("root_id", $loggedId);
				} else {
					session()->set("root_id", 0);
				}
				
				admin()->login($admin);
				
				return $this->redirect(['/admin/index/index']);
			} else {
				throw new NotFoundHttpException('Страница не найдена.');
			}
		} else {
			throw new NotFoundHttpException('Страница не найдена.');
		}
    }	
	
    public function actionAccessDenied()
    {
		return $this->render('access_denied');
    }	
	
}