<?php

namespace app\widgets\vstab;

use Yii;
use yii\base\Widget;
use yii\helpers\Json;
use yii\helpers\Url;


class Manager extends Widget {

    public $localized = false;
    public $textareaAttributes = [];
    public $editorAttributes = [];
    public $inputAttributes	= [];
    public $imageAttributes	= [];
    public $filesAttributes	= [];
    public $selectAttributes= [];
    public $labelAttributes	= [];
    public $labelCreate	= '';
    public $labelUpdate	= '';
    public $labelItem	= '';
    
	public $model_id;
    public $modelName;
    public $fullModelName;
    public $modelAttribute = null;
    public $multilingual = 1;

    public $items;

 	/**
	 * Registers the needed assets
	 */
	public function registerAssets()
	{
		$view = $this->getView();
		VstabAsset::register($view);
	}
	
 	public function init() {
		$this->registerAssets();
		
		return parent::init();
	} 

    public $htmlOptions = array();

    /** Render widget */
    public function run() {

        $items = array();
        foreach ($this->items as $item) {
            $items[] = $item->getDataInfo();
        }
		
		//d($items);
		
        $data = array(
			'createUrl' 		=> '/?r=vstab/create&owner_id='.$this->model_id,
			'deleteUrl' 		=> '/?r=vstab/delete',
			'updateUrl' 		=> '/?r=vstab/update',
			'arrangeUrl' 		=> '/?r=vstab/order',
			'getUrl' 			=> '/?r=vstab/get',
            'items' 			=> $items,
            'labelItem' 		=> $this->labelItem,
            'labelCreate' 		=> $this->labelCreate,
            'labelUpdate' 		=> $this->labelUpdate,
            'textareaAttributes'=> $this->textareaAttributes,
            'editorAttributes'	=> $this->editorAttributes,
            'inputAttributes' 	=> $this->inputAttributes,
            'selectAttributes' 	=> $this->selectAttributes,
            'imageAttributes' 	=> $this->imageAttributes,
            'filesAttributes' 	=> $this->filesAttributes,
            'labelAttributes' 	=> $this->labelAttributes,
            'modelName' 		=> $this->modelName,
            'fullModelName'		=> $this->fullModelName,
            'languages' 		=> Yii::$app->params['otherLanguages'],
            'language' 			=> Yii::$app->language,
            'modelAttribute' 	=> $this->modelAttribute,
            'multilingual' 	=> $this->multilingual,
        );

        if (Yii::$app->request->enableCsrfValidation) {
            $data['csrfTokenName'] 	= Yii::$app->request->csrfParam;
            $data['csrfToken'] 		= Yii::$app->request->getCsrfToken();
        }
		
        $options = Json::encode($data);
		
		$this->view->registerJsFile('/admin/js/jquery-ui-1.9.2.custom.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); 
	
		$this->view->registerJs("
			$('#{$this->id}').vstabManager({$options});		
			
		", \yii\web\View::POS_END, 'vstab-manager-'.$this->id);			
		
		$data['id'] = $this->id;
        echo $this->render('manager', $data);
    }

}
