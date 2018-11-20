<?php

namespace app\modules\structure\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use app\components\AdminController;
use app\modules\structure\models\Block as Block;
use app\modules\structure\models\Page as Page;

/**
 * BlockController implements actions for Structure model.
 */
class BlockController extends AdminController
{	
	const DOES_NOT_SETTINGS = 'у виджета нет настроек';
		
	public function getAccessActionMap()
	{
		return parent::getAccessActionMap() + [
			'getwidgetconfig'	=> 'update',
			'get'				=> 'update',
			'clear' 			=> 'delete',
		];
	}	
	
    public function actions()
    {
         $actions = parent::actions() + [];
		
		return $actions;
    }	

	public function actionGet() 
	{
		if (Yii::$app->request->isAjax) {
			
			$id 		= (int)Yii::$app->request->post('id');
			$block_id 	= Yii::$app->request->post('block_id');
			
			$result = ['success' => 0,];			
			
			$result['success'] = 1;
			$block = Block::find()->where('alias=:alias AND page_id=:page_id')->addParams([':alias' => $block_id, ':page_id' => $id])->one();
			if (!empty($block)) { 
				switch ((int)$block->type_id) {
					case 1: 
						$content = unserialize($block->content);
						
						$result['alias'] = $content[0];
						$result['parameters'] = $content;
						break;
					case 2:
						$result['content'] = $block->content;
						break;
				}
			}
			
			return json_encode($result);
		}
	}
	
	/**
	 * Получить диалог настроек виджета (ajax)
	 */
	public function actionGetwidgetconfig()
	{
		$widgetAlias 	= Yii::$app->request->post('widgetAlias', null);
		$pageId 		= Yii::$app->request->post('pageId', null);
		$blockId 		= Yii::$app->request->post('blockId', null);
		
		if (Yii::$app->request->isAjax) {
			
			$attributes = [];
			$config = self::DOES_NOT_SETTINGS;
			
			if (!empty($pageId) && !empty($blockId)) {
				//извлекаем параметры из контекстного блока страницы, чтобы настроить конфиги виджета
				$block = Block::find()->where('alias=:blockId AND page_id=:pageId')->addParams([':blockId' => $blockId, ':pageId' => $pageId])->one();

				if (!empty($block)) {
					
					$attributes = unserialize($block->content);
					$widgetAlias = !empty($attributes[0]) ? $attributes[0] : '';
					unset($attributes[0]);
				}
			}
			
			if (!empty($widgetAlias)) {

				$path = Yii::getAlias('@app') . '/modules/'.  $widgetAlias . '/front/config.php';
				if (file_exists($path) && is_readable($path)) {
					$classNamespace 	= '\app\modules\\' . $widgetAlias . '\front\Widget';
					$attributes['model'] = new $classNamespace;
					$config = $this->renderFile($path, $attributes);
				}
				return json_encode(['config' => $config, 'path'=>$path]);				
			}
		}
		Yii::$app->end();
	}

	/**
	 * Обновляем данные на соотвествующей странице, в соответствующем блоке
	 */
	public function actionUpdate()
	{
		//получаем айди страницы
		$pageId = Yii::$app->request->post('id', null);
		
		//получаем название (айди) блока
		$blockId = Yii::$app->request->post('block_id', null);
		
		
		$logAction = 'structure.page.';
		
		//получить тип контента
		$typeId		= Yii::$app->request->post('type_id', null);
		$content 	= Yii::$app->request->post('content', null);
		
		//работаем только если это аяксовый запрос и указанны параметры страницы и блока
		if (Yii::$app->request->isAjax && !empty($pageId) && !empty($blockId)) {
			$configParameters 	= '';
			$response 			= [];
			
			
			if ($typeId == 1) {
				$configParameters 	= self::__parseConfigParameters(Yii::$app->request->post('config_parameters', null));
				$classNamespace 	= '\app\modules\\' . $content . '\front\Widget';
				$modelWidget 		=  new $classNamespace;
				
				$modelWidget->attributes = $configParameters;
	
				if (!$modelWidget->validate()) {
					$response['status'] = -1;
					$response['errors'] = $modelWidget->errors;
					
					echo json_encode($response);
					return;					
				}
			}
			
			//получаем страницу
			$page = Page::findOne($pageId);
			if (empty($page)) {
				$response['status'] = 0;
				$response['errors'] = ['page_id', self::PAGE_NOT_EXISTS];
			}
		
			//вынимаем блок
			$model = Block::find()->where('alias = :blockId AND page_id = :pageId')->addParams([':pageId' => $page->id, ':blockId' => $blockId])->one();
			
			//если указанного блока нет в базе данных, то создаем его
			if (empty($model)) {
				$model = new Block;
				$model->attributes = [
					'page_id' 	=> $page->id,
					'alias' 	=> $blockId,					
				];
				
				$logAction.= 'createBlock';
			} else {
				$logAction.= 'updateBlock';
			}
			
			$model->type_id = $typeId;
			
			//добавляем серилайзнутые конфиги, если они есть в переданных данных
			//конфиги идут только при вставке виджета в блок
			switch ($typeId) {
				case 1: //widget
					$model->content = '';
					if (!empty($content)) {
						$content = [$content];						
						if (!empty($configParameters)) { $content += $configParameters;}
						$content = str_replace('[]','', $content);
						
						$model->content = serialize($content);
					}
					break;
				case 2: //text
					$model->content = $content;
					break;
			}
			
			//сохраняем данные
			$result = $model->save();
			
			$response['status'] = $result;
			if (!$result) { $response['errors'] = $model->getErrors();}

			$info = sprintf('контент в блок %s на странице %s', $blockId, $page->alias);
			$info = $result ? sprintf('Вставлен %s', $info) : sprintf('Ошибка при попытке вставить %s', $info);
			
			\app\components\Log::save($pageId, $logAction);
					
			return json_encode($response);
		}
	}
	
	/**
	 * Очистить блок от контента
	 */
	public function actionClear()
	{
		//получаем номер страницы и алиас блока, который клиент хочет очистить
		$pageId = Yii::$app->request->post('id', null);
		$blockId = Yii::$app->request->post('blockId', null);
		
		$data = ['result' => 0];
		if (Yii::$app->request->isAjax && !empty($pageId) && !empty($blockId)) {
			
			//считываем затребованный блок
			$block = Block::find()->where('alias =:blockId AND page_id =:pageId')->addParams([':pageId' => $pageId, ':blockId' => $blockId])->one();
			
			//запоминаем алиас блока, чтобы сохранить в логе информацию об операциях над ним
			$pageAlias = '';
			if (!empty($block)) { $pageAlias = $block->page->alias;}
			
			$result = false;
			if (empty($block)) { $data['errorMessage'] = 'Блок уже пуст';}
			if ($block) { $result = $block->delete();}
				
			if ($result) { 
				$data['result'] = 1;
				
				\app\components\Log::save($pageId, 'structure.page.deleteBlock');
			}				
		}
		
		return json_encode($data);
	}
	

	private static function __parseConfigParameters($data)
	{
		$data = urldecode($data);
		
		$results = [];
		if (!empty($data)) {
			$pares = explode('&', $data);
			if (count($pares)) {
				foreach ($pares as $pare) {
					$pare = explode('=', $pare);
					if (preg_match('!(.+)\[([^\]]+)\]!', $pare[0], $key)) {
						$results[$key[1]][$key[2]] = array_pop($pare);
					} 
					elseif (count($pare)) { $results[array_shift($pare)] = array_shift($pare);}
				}
			}
		}
		return $results;
	}	

}
