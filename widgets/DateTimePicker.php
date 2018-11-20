<?php

namespace app\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

class DateTimePicker extends InputWidget
{
 	public $value;
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
		$this->options['class'] = 'form-control datetimepicker';
		$this->options['style'] = 'max-width: 160px;';
		
		$value = ''; 
		if (!empty($this->model)) {
			$value = $this->model->{"{$this->attribute}"};
		} else if (!empty($this->value)) {
			$value = $this->value;
		}
	
		if (!empty($value)) {
			if (is_numeric($value)) {
				$value = date('Y-m-d H:i:s', $value);
			}
		
			$this->options['value'] = $value;
		}
		
        $input = 'textInput';
		
		$input = $this->getInput($input);
		
		$this->view->registerJsFile('/admin/bootstrap/js/bootstrap-datetimepicker.min.js',['depends' => [\yii\web\JqueryAsset::className()]]);
		
		$this->view->registerJs("
			$('.datetimepicker').datetimepicker({
				format: 'yyyy-mm-dd hh:ii:ss',
				language: 'ru'
			}).on('changeDate', function(e) {
				$(this).datetimepicker('hide');
			});		
			
			$('.ui-datepicker-trigger').click(function(){
				$(this).prev().trigger('focus');
			})
		", \yii\web\View::POS_END, 'datetimepicker');		

		return $input . '<button type="button" class="ui-datepicker-trigger">Дата</button>';
    }
		

}
