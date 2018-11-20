<?php

namespace app\modules\structure\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use app\components\AdminController;
use app\helpers\Page as PageHelper;
use app\modules\structure\models\Page;
use app\modules\structure\models\PageMoveForm;

/**
 * PageController implements actions for Structure model.
 */
class PageController extends AdminController
{	
	public $structure_id, $title, $head, $description, $h1, $is_canonical, $js_code, $activeWidgetData;	
	
	public $breadcrumbs = [];
	public $domainData = [];

	private $_model = null;

    public function actions()
    {
         $actions = parent::actions() + [];
		
		return $actions;
    }	

	public function actionCreate() 
	{
		if (r()->isAjax && r()->isPost) {
			$model = new Page;
			$model->load(r()->post());
			//формируем структурный айдишник для новой страницы на основе родительского айди
			$model->structure_id = \app\helpers\Page::buildNewPageStructureId($model->parent_id);
			
			$response = [];
			
			if ($model->save()) {
				Yii::$app->session->setFlash('success', 'Успешно создано');
				
				//Записываем лог действий
				\app\components\Log::save($model->id);
				
				$response['success'] = 1;
				$response['pageId'] = $model->id;
				
			} else {
				$response['status'] = 0;
				$response['errors'] = $model->errors;
			}
			
			return json_encode($response);
		}
	}
	
	public function actionUpdate() 
	{
		if (r()->isAjax) {
			$id 	= (int) r()->getQueryParam('id');
			$mode 	= r()->getQueryParam('mode');
			$model = $this->loadModel($id);
			
			if (empty($model)) {
				//throw new NotFoundHttpException('Страница не найдена.');
			}
			
			$response = ['success'=>0];
			
			if ($mode == 'save') {			
				if ($model->load(r()->post()) && $model->save()) {
					Yii::$app->session->setFlash('success', 'Успешно создано');
					//Записываем лог действий
					\app\components\Log::save($model->id);
					
					$response['success'] = 1;
					$response['pageId'] = $model->id;
					
				} else {
					$response['success'] = 0;
					$response['errors'] = $model->errors;
				}
			} else {
				$response['success'] = 1;
				$response['attributes'] = $model->attributes;
				foreach ($model->behaviors['ml']->attributes as $attr) {
					
					$response['attributes'][$attr] = $model->$attr;
					
					foreach (Yii::$app->params['otherLanguages'] as $lang=>$langTitle) {
						$langAttr = $attr . '_' . $lang;
						$response['attributes'][$langAttr] = $model->$langAttr;
					}
				}				
				
				
			}
			
			return json_encode($response);
		}
	}
	
	public function actionMove() 
	{
		if (r()->isAjax) {
			$id 	= (int) r()->getQueryParam('id');
			$mode 	= r()->getQueryParam('mode');
			$model = $this->loadModel($id);
			
			if (empty($model)) {
				//throw new NotFoundHttpException('Страница не найдена.');
			}
			
			$response = ['success'=>0];
			
			if ($mode == 'save') {		
				$modelMove = new PageMoveForm;
				$modelMove->page_id = $id;
				$modelMove->parent_id = r()->get('parent_id');
				
				if ($modelMove->move()) {
					//Записываем лог действий
					\app\components\Log::save($id);
					
					$response['success'] = 1;
					$response['pageId'] = $id;
					
				} else {
					$response['success'] = 0;
					$response['errors'] = $modelMove->errors;
				}
			} else {
				$response['success'] 	= 1;
				$response['pages'] 		= Page::getListMove($model);
			}
			
			return json_encode($response);
		} 
	}
	
	public function actionDelete() 
	{
		if (r()->isAjax) {
			$id = (int) r()->post('id');
			
			$model = $this->loadModel($id);
			
			if (empty($model)) {
				throw new NotFoundHttpException('Страница не найдена.');
			}
			
			$model->delete();
			
			Yii::$app->session->setFlash('success', 'Успешно удалено');
			
			//Записываем лог действий
			\app\components\Log::save($model->id);
			
			$response = ['success'=>1];

			
			return json_encode($response);
		}
	}
	
	/**
	 * Событие - отрисовующее востребованный пейдж в айфреме
	 * @param integer $id -- порядковый номер страницы
	 */
	public function actionView($id = null)
	{
		$this->layout = '@app/views/layouts/main';
				
		//получаем контент страницы
		$content = $this->renderContentPage($id);
			
		return $this->render('view', [
			'admin' => true, 
			'content' => $content, 
			'pageId' => $id,
		]);
	}	
	
	/**
	 * Отрисовываем запрошенную страницу
	 * @param integer $pageId -- порядковый номер страницы
	 * @return string
	 */
	public function renderContentPage($pageId = null)
	{
		$result = 'Невозможно отобразить страницу';
		
		if (empty($pageId)) { $pageId = 1;}
		
		//получаем затребованную страницу
		$settings = Page::find()->with('blocks')->where('id=:id')->addParams([':id'=>$pageId])->one();
       
		//если получили настройки пейджи...
		if (!empty($settings)) {
			
			//получаем путь к файлу-шаблону страницы
			$templateName = Yii::getAlias('@app') . '/views/templates/' . Yii::$app->params['templates'][$settings->template_id]['alias'] . '.php';
			
			//получаем вектор с заполненными переменными шаблона
			$preparedBlocks = PageHelper::prepareBlocks($settings->blocks, true);
			
			//получаем вектор пустых переменных и оформляем их в виде блоков
			$emptyVariables = array_diff(PageHelper::fetchAllTemplateVariables($templateName), array_keys($preparedBlocks));
			
			$emptyBlocks = [];
			if (is_array($emptyVariables) && count($emptyVariables)) {
				foreach ($emptyVariables as $emptyVar) { $emptyBlocks[$emptyVar] = '<div id="block_'.$emptyVar.'" class="acms_content-block"><div class="clear"><span class="structure-block-title">'.$emptyVar.'</span></div></div>';}
			}
			
			//мерджим с заполненными блоками
			$preparedBlocks += (array)$emptyBlocks;
			
			//подключаем шаблон и внедряем значения
			$result = $this->implementVariables($templateName, $preparedBlocks);
		}
		
		return $result;
	}	
	
	public function getVariables() { return [];}	

	/**
	 * Заполняем блоки в шаблоне, выводом полученным из виджетов
	 * @param string $templateName
	 * @param array $data
	 * @return string
	 */
	private function implementVariables($templateName, $data)
	{
		ob_start();
		ob_implicit_flush(false);
		require($templateName);
		$content = ob_get_clean();
		foreach (array_keys($data) as $key) { $content = str_replace(sprintf(PageHelper::EMBED_TEMPLATE, $key), $data[$key], $content);}
		
		return $content;
	}	
	
	/**
	 * Загружаем модель страницы
	 * @return CActiveRecord
	 */
	protected function loadModel($id)
	{
		if ($this->_model === null) {
			
			if (!empty($id)) { 
				$this->_model = Page::find()->where('id=:id', ['id'=>$id])->multilingual()->one();
			}
			if ($this->_model === null) { 
				throw new NotFoundHttpException('Страница не найдена.');
			}
			//установить группы пользователей, которым разрешен доступ к выбранной пейдже
			//$this->_model->getGroupAccessed();
		}
		return $this->_model;
	}	
	
}
