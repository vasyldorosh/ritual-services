<?php

namespace app\modules\structure\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use app\components\AdminController;
use app\modules\structure\models\Archive;

/**
 * ArchiveController implements actions for Structure model.
 */
class ArchiveController extends AdminController
{	
	public function actionCreate() 
	{
		if (Yii::$app->request->isAjax) {
			
			\app\components\Log::save(Archive::add());
			
			$response = ['success'=>1];
			echo json_encode($response);
		}
	}
	
	
	public function actionDelete() 
	{
		if (Yii::$app->request->isAjax) {
			$id = (int) Yii::$app->request->get('id');
			
			$model = Archive::findOne($id);
			
			if (empty($model)) {
				throw new NotFoundHttpException('Страница не найдена.');
			}
			
			$model->delete();
			
			//Записываем лог действий
			\app\components\Log::save($model->id);
			
			return $this->actionIndex();
		}
	}
	
	public function actionRestore() 
	{
		if (Yii::$app->request->isAjax) {
			$id = (int) Yii::$app->request->get('id');
			if (Archive::restore($id)) {
				$response = ['success'=>1];
			} else {
				$response = ['success'=>0, 'error'=>'Архив не найден'];
			}

			\app\components\Log::save($id);
			echo json_encode($response);
		}
	}	
	
	public function actionIndex() 
	{
		if (Yii::$app->request->isAjax) {
			return $this->renderPartial('index');			
		}
	}	
	
}
