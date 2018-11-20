<?php

namespace app\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class Tagedit extends InputWidget
{
 	public $options;
	public $model;
	public $attribute;
		
	protected $defaultOptions = [];	
		
    public function init()
    {
        $this->defaultOptions = [			
			'typeahead' => true,
			'typeaheadAjaxSource' => '/?r=tag/tag/autocomplete',
			'hiddenTagListName' =>  \yii\helpers\StringHelper::basename(get_class($this->model)) . '['.$this->attribute.']'	
		];		
		
        echo $this->renderInput();
    
		return parent::init();
	}

    protected function renderInput()
    {
		$this->options['class'] = 'form-control';
	
		$model = $this->model;
		$attribute = $this->attribute;
		

		$options = ArrayHelper::merge($this->defaultOptions, $this->options);	

		$options = Json::encode($options);
		
        $input = 'textInput';
		
		$input = $this->getInput($input);
		
		$this->view->registerJsFile('/admin/js/tagmanager.js',['depends' => [\yii\web\JqueryAsset::className()]]);
		
		$this->view->registerJs("
			jQuery('.tm-input').tagsManager({$options});				
		", \yii\web\View::POS_END, 'tm-input');			
		
		return '<input style="max-width: 200px;" type="text" name="tags" class="tm-input" value="" autocomplete="off" placeholder="Теги">
				<br/>
				После ввода тега поставьте или нажмите , space or Enter	
				<br/>'.$input;
		
	}
		

}
