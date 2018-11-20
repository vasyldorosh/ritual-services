<?php
namespace app\widgets;

use app\components\AccessControl;
use yii\helpers\Html;

class Buttons
{
	public static function create() {
		
		$rule = (\Yii::$app->controller->module->id) . '.' . (\Yii::$app->controller->id) . '.' . 'create';
		
		$html = '';
		if (AccessControl::can($rule)) {
			$html = '<p>';
			$html.= Html::a('Создать', ['create'], ['class' => 'btn btn-xs btn-success']);
			$html.= '</p>';
		}
		
		return $html;
	}
	
	public static function actions($id) {
		
		$rule = (\Yii::$app->controller->module->id) . '.' . (\Yii::$app->controller->id) . '.';
		$html = '<p>';
		
		$baseUrl = '/?r=' . (\Yii::$app->controller->module->id) . '/' . (\Yii::$app->controller->id) . '/';
		
		foreach (
			[
				'index'		=>	['label'=>'к списку','class'=>'btn-default'], 
				'create'	=>	['label'=>'создать','class'=>'btn-success'], 
				'update'	=>	['label'=>'редактировать','class'=>'btn-info'], 
				'delete'	=>	['label'=>'удалить','class'=>'btn-warning'], 
			] 
			as $action=>$data) {
			
			if (AccessControl::can($rule.$action)) {
				$params = in_array($action, ['index', 'create']) ? '' : "&id={$id}";
				
				$url = $baseUrl . $action . $params;
				
				$html.= ' ' . Html::a($data['label'], $url, ['class' => 'btn btn-xs ' . $data['class']]);
			}
		}
			
		$html.= '</p>';
			
		return $html;
	}
	
	public static function formSubmit($model, $config=[]) {
		
		$html = '<div class="form-actions">';
		
		$buttons = [];
		
		
		$buttons[] = Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-success', 'name'=>'yt0']);
		$buttons[] = Html::submitButton('Применить', ['name'=>'apply', 'class'=>'btn btn-apply']);
		$buttons[] = Html::a('Отмена', isset($config['cancelUrl']) ? $config['cancelUrl'] : ['index'], ['class'=>'btn-cansel btn']);
		
		$html.= implode('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $buttons);
		$html.= '</div>';
			
		return $html;
	}
}