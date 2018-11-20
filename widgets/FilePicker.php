<?php

namespace app\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

class FilePicker extends InputWidget
{
 	public $options;
	public $model;
	public $attribute;
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
		
        $input = 'fileInput';
		
		$input = $this->getInput($input);

		if (!empty($model->$attribute)) {
			$input.= \yii\helpers\Html::a('Скачать', $model->getFileUrl($attribute));
			if ($this->is_delete)
				$input.= '<label><input value="1" type="checkbox" name="'.$model->formName().'[is_delete_'.$attribute.']"> Удалить</label>';
		}			
		
		return $input;
		
	}
		

}
