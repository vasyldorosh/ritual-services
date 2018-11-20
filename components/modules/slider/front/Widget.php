<?php

namespace app\modules\slider\front;

use Yii;
use app\components\BaseWidget;
use app\modules\slider\models\Slider;

class Widget extends BaseWidget
{	
	public $slider_id;
 
    /**
     * return string
     */
    public function getName()
	{
		return 'Слайдер';
	}
	
    /**
     * return array
     */
    public function attributes()
	{
		return parent::attributes() + [
			'slider_id' 	=> 'Слайдер',
		];
	}	
	
    /**
     * return array
     */
    public function rules()
	{
		$rules = parent::rules();
		if ($this->action == 'index') {
			$rules[] = [['slider_id'], 'required'];
		} else {
			$this->slider_id = '';
		}
 	
		return $rules;
	}	
	
    /**
     * return array
     */
    public static function getActions()
	{
		return [
			'index' => 'Показать слайдер',
		];
	}
	
    public function actionIndex()
	{
		$slider = Slider::getItem($this->slider_id);
		
		if (!empty($slider)) {
			$data['slides'] = Slider::getSlides($this->slider_id);	
			echo $this->render('index', $data);
		}
	}
	
}