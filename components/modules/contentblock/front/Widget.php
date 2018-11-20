<?php

namespace app\modules\contentblock\front;

use Yii;
use app\components\BaseWidget;
use app\modules\contentblock\models\ContentBlock;

class Widget extends BaseWidget
{	
 
	public $action = 'index';
	
	public $template = 'index';
	
	public $block_id = null;
	
    /**
     * return string
     */
    public function getName()
	{
		return 'Контентные блоки';
	}
	
    /**
     * return array
     */
    public function attributes()
	{
		return parent::attributes() + [
			'template' => 'Шаблон',
			'block_id' => 'Блок',
		];
	}	 
	
    /**
     * return array
     */
    public function rules()
	{
		$rules = parent::rules();
		if (in_array($this->action, ['index'])) {
			$rules[] = [['template', 'block_id'], 'required'];
		} else {
			$this->template = '';
			$this->block_id = '';
		}
 	
		return $rules;
	}	
	
    /**
     * return array
     */
    public static function getTemplates()
	{
		return [
			'index' 	=> 'Пустой (вывод только контента)',
			'article' 	=> 'Article',
		];
	}	
	
    /**
     * return array
     */
    public static function getActions()
	{
		return [
			'index' => 'Показать контент блок',
		];
	}
	
    /**
     * action index
     */
    public  function actionIndex()
	{
		echo $this->render('templates/'.$this->template, [
			'data' => $this->getData($this->block_id, $this->template),
		]);
	}
	
	public function getData($block_id)
	{
		$block_id = (int) $block_id;
		if (empty($block_id)) {
			return [];
		}
		
		$tag 	= ContentBlock::getTag() . '_' . $block_id;
		
		$key 	= $tag . '_getData_' . l();
		$data 	= cache()->get($key);
		
		if ($data === false) {
			$data = [];
			
			$model = ContentBlock::find()->where(['id'=>$block_id, 'is_active'=>1])->one();
			
			if (!empty($model)) {
				$data['content'] = $model->content;
			}
			
			cache()->set($key, $data, 0, td(ContentBlock::getTag()));
		}
		
		return $data;
	}
	
}