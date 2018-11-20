<?php 
namespace app\components;

use Yii;
use app\modules\admin\models\Log as ModelLog;

class Log
{	
	/**
	 * Сохранить лог
	 * @param integer $id
	 * @return boolean -- результат действия
	 */
	public static function save($id, $action='', $params=[])
	{
		$admin_id = isset($params['admin_id'])? $params['admin_id'] : admin()->id;	
			
			
		$model 	= new ModelLog();
		$model->model_id	= $id;
		$model->admin_id	= $admin_id;
		$model->action 		= !empty($action) ? $action : Yii::$app->controller->module->id . '.' . Yii::$app->controller->id . '.' . Yii::$app->controller->action->id;
		$model->created_at 	= time();
		$model->ip 			= Yii::$app->request->userIP;
		
		if (substr_count($model->action, '.') != 2 && !isset($params['skip'])) {
			return false;
		}
		
		return $model->save();
	}
	
}