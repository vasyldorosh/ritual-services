<?php

namespace app\widgets\gallery;

use Yii;
use yii\base\Widget;
use yii\helpers\Json;
use yii\helpers\Url;


class Manager extends Widget {

    public $hasTitle		= false;
    public $hasDescription	= false;
    
	public $model_id;
    public $photoModelName;
    public $photoFullModelName;
    public $modelAttribute = 'gallery_id';

    public $photos;

 	/**
	 * Registers the needed assets
	 */
	public function registerAssets()
	{
		$view = $this->getView();
		Asset::register($view);
	}
	
 	public function init() {
		parent::init();
		
		$this->registerAssets();
	} 

    public $htmlOptions = array();

    /** Render widget */
    public function run() {

        $photos = array();
        foreach ($this->photos as $photo) {
            $row = array(
                'id' => $photo->id,
                'rank' => $photo->rank,
                //'name' => (string) $photo->name,
                //'description' => (string) $photo->description,
                'preview' => $photo->getImageUrl('image', 134, 100, 'crop'),
            );
			
			/*
			foreach (Yii::app()->params->otherLanguages as $lang=>$t) {
				$row["name_$lang"] = $photo->{"name_$lang"};
				$row["description_$lang"] = $photo->{"description_$lang"};
			}
			*/
			
			$photos[] =  $row;
        }
		
        $data = array(
			'hasTitle' 			=> $this->hasTitle,
			'hasDescription' 	=> $this->hasDescription,
			'uploadUrl' 		=> '/?r=gallery/upload&gallery_id='.$this->model_id,
			'deleteUrl' 		=> '/?r=gallery/delete',
			'updateUrl' 		=> '/?r=gallery/update',
			'arrangeUrl' 		=> '/?r=gallery/order',
            'nameLabel' 		=> 'Название',
            'descriptionLabel' 	=> 'Описание',
            'linkLabel' 		=> 'Ссылка',
            'photos' 			=> $photos,
            'photoModelName' 	=> $this->photoModelName,
            'photoFullModelName'=> $this->photoFullModelName,
            'languages' 		=> Yii::$app->params['otherLanguages'],
            'language' 			=> Yii::$app->language,
            'modelAttribute' 	=> $this->modelAttribute,
        );

        if (Yii::$app->request->enableCsrfValidation) {
            $data['csrfTokenName'] 	= Yii::$app->request->csrfParam;
            $data['csrfToken'] 		= Yii::$app->request->getCsrfToken();
        }
		
        $options = Json::encode($data);
		
		$this->view->registerJsFile('/admin/js/jquery-ui-1.9.2.custom.min.js',['depends' => [\yii\web\JqueryAsset::className()]]); 
	
		$this->view->registerJs("
			$('#{$this->id}').galleryManager({$options});		
			
		", \yii\web\View::POS_END, 'gallery-manager');			
		
		$data['id'] = $this->id;
        echo $this->render('upload', $data);
    }

}
