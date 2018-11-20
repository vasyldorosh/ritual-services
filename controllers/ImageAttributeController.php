<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\components\behaviors\ImageAdaptiveUploadBehavior;

class ImageAttributeController extends Controller
{
    public function actionDelete()
    {	
		if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
			$response = array();
			
			$modelName = Yii::$app->request->post('model');
			//if (class_exists($modelName)) {
			if (true) {
				//$model = CActiveRecord::model($modelName);
				$attribute = Yii::$app->request->post('attribute');
				//if ($model->hasAttribute($attribute)) {
				if (true) {
					$size = Yii::$app->request->post('size');
					$lang = Yii::$app->request->post('lang');
					$id = (int)Yii::$app->request->post('id');
					$i18n = (int)Yii::$app->request->post('i18n');
					$tableName = $modelName::tableName();
					$sql = "SELECT {$attribute} AS img FROM {$tableName} WHERE id={$id}";
					$row = Yii::$app->db->createCommand($sql)->queryOne();
									
					if (!empty($row)) {
						$images = !empty($row['img'])?json_decode($row['img'], 1):[];
						if (isset($images[$size])) {
							if (isset($images[$size]['path'])) {
								ImageAdaptiveUploadBehavior::deleteImage($images[$size]['path']);
							}
						
							unset($images[$size]);
							$images = (!empty($images))?json_encode($images):'';
							$sql = "UPDATE {$tableName} SET {$attribute}='{$images}' WHERE id={$id}";	
							Yii::$app->db->createCommand($sql)->execute();
							
							$response['success'] = 1;
							$response['message'] = "Успешно удалено";							
							
						} else {
							$response['success'] = 0;
							$response['error'] = "Изображение {$size} не найдено";						
						}
					} else {
						$response['success'] = 0;
						$response['error'] = "Данные не найдены";	
					}
					
				} else {
					$response['success'] = 0;
					$response['error'] = "Атрибут {$attribute} в моделе {$modelName} не найден";					
				}
			} else {
				$response['success'] = 0;
				$response['error'] = "{$modelName} не найдена";			
			}
			
			echo json_encode($response);	
		}
    }

	public function actionFields()
    {	
		$configs = require_once(dirname(__FILE__).'/../../config/image.php');
		foreach ($configs as $modelName => $attributes) {
			foreach ($attributes as $attribute => $attributeConfig) {
				$model = CActiveRecord::model($modelName);
				$tableName = $model->tableName();
				if (!$model->hasAttribute($attribute)) {
					$sql = "ALTER TABLE  {$tableName} ADD  `{$attribute}` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
					Yii::$app->db->createCommand($sql)->execute();
					Yii::$app->db->schema->getTable($tableName, true);	
					
					if ($attributeConfig['i18n']) {
						$tableNameI18n = $tableName . '_i18n';
						$sql = "ALTER TABLE  {$tableNameI18n} ADD  `{$attribute}` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
						Yii::$app->db->createCommand($sql)->execute();
						Yii::$app->db->schema->getTable($tableNameI18n, true);	
					}	
				}
			}
		}
	}	

	
}
