<?php

namespace app\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

class ImagePicker extends InputWidget
{
 	public $options;
	public $model;
	public $attribute;
	public $resize = true;
	public $width  = 120;
	public $height  = 00;
	public $is_delete = false;
		
    public function init()
    {
        echo $this->renderInput();
    
		return parent::init();
	}

    protected function renderInput()
    {
		$this->options['class'] = 'form-control';
		
		$model = $this->model;
		$attribute = $this->attribute;
		
		if (!empty($value)) {
			if (is_numeric($value)) {
				$value = date('Y-m-d', $value);
			}
		
			$this->options['value'] = $value;
		}
		
        $input = 'fileInput';
		
		$input = $this->getInput($input);

		if (!empty($model->$attribute)) {
			if ($this->resize)
				$input.= \yii\helpers\Html::img($model->getImageUrl($attribute, $this->width, $this->height, 'resize'));
			else
				$input.= \yii\helpers\Html::img($model->getImageUrl($attribute));

			if ($this->is_delete)
				$input.= '<label><input value="1" type="checkbox" name="'.$model->formName().'[is_delete_'.$attribute.']"> Удалить</label>';			
		}			
		
		return $input;
		
	}
		

}
