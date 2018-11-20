<?php

namespace app\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

class DatePicker extends InputWidget
{
 	public $options;
	public $model;
	public $attribute;
		
    public function init()
    {
        echo $this->renderInput();
    
		return parent::init();
	}

    protected function renderInput()
    {
		$this->options['class'] = 'form-control datepicker';
		$this->options['style'] = 'max-width: 100px;';
		
		$value = $this->model->{"{$this->attribute}"};
		
		if (!empty($value)) {
			if (is_numeric($value)) {
				$value = date('Y-m-d', $value);
			}
		
			$this->options['value'] = $value;
		}
		
        $input = 'textInput';
		
		$input = $this->getInput($input);
		
		$this->view->registerJsFile('/admin/bootstrap/js/bootstrap-datepicker.js',['depends' => [\yii\web\JqueryAsset::className()]]);
		
		$this->view->registerJs("
			$('.datepicker').datepicker({
				format: 'yyyy-mm-dd',
				language: 'ru'
			}).on('changeDate', function(e) {
				$(this).datepicker('hide');
			});		
			
			$('.ui-datepicker-trigger').click(function(){
				$(this).prev().trigger('focus');
			})
		", \yii\web\View::POS_END, 'datepicker');		

		return $input . '<button type="button" class="ui-datepicker-trigger">Дата</button>';
    }
		

}
