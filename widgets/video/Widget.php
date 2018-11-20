<?php

namespace app\widgets\video;

use Yii;
use yii\helpers\Json;
use yii\helpers\Url;


class Widget extends \yii\base\Widget {

    //common
	public $mode 				= 'frontend';
    public $videos;
	
	//frontend
	public $modelName 			= 'Video';
    public $attributeName 		= 'post_videos';
    public $attributeDeletedIds = 'deleted_videos_ids';

	//backend
    public $hasTitle		= false;
    public $hasDescription	= false;
    public $model_id;
    public $videoModelName;
    public $videoFullModelName;
    public $modelAttribute = 'gallery_id';
	
 	public function init() {
		parent::init();
			
		if ($this->mode == 'frontend') {
			AssetFrontend::register($this->getView());
		} else {
			AssetBackend::register($this->getView());
		}	
	} 

    public function run() {
 		if ($this->mode == 'frontend') {
			$this->frontend();
		} else {
			$this->backend();
		}	   
	}
	
	private function backend()
	{
        $videos = [];
		foreach ($this->videos as $item) {
			$videos[] = [
				'id' => $item->id,
				'rank' => $item->rank,
				'preview' => $item->getImageUrl('image', 140, 85, 'crop'),
			];
		}
		
		$options = [
			'videos' 			=> $videos,
			'hasTitle' 			=> $this->hasTitle,
			'hasDescription' 	=> $this->hasDescription,
			'createUrl' 		=> '/?r=video/create&gallery_id='.$this->model_id,
			'deleteUrl' 		=> '/?r=video/delete',
			'orderUrl' 			=> '/?r=video/order',
            'nameLabel' 		=> 'Название',
            'descriptionLabel' 	=> 'Описание',
            'videoModelName' 	=> $this->videoModelName,
            'videoFullModelName'=> $this->videoFullModelName,
            'languages' 		=> Yii::$app->params['otherLanguages'],
            'language' 			=> Yii::$app->language,
            'modelAttribute' 	=> $this->modelAttribute,			
            'model_id' 			=> $this->model_id,			
		];
		
        if (Yii::$app->request->enableCsrfValidation) {
            $options['csrfTokenName'] 	= r()->csrfParam;
            $options['csrfToken'] 		= r()->getCsrfToken();
        }	

		$options =  Json::encode($options);
			
		$this->view->registerJs("
			$('#".$this->id."').videoManager({$options});		
			
		", \yii\web\View::POS_END, 'video-manager-'.$this->mode);		
		
		
        echo $this->render('backend', []);		
	}

	private function frontend()
	{
        $options = Json::encode([
			'modelName' => $this->modelName,
			'attributeName' => $this->attributeName,
			'attributeDeletedIds' => $this->attributeDeletedIds,
		]);
	
		$this->view->registerJs("
			$('#videoManager').videoManager({$options});		
			
		", \yii\web\View::POS_END, 'video-manager');		
		
		
        echo $this->render('frontend', [
			'videos'=>$this->videos,
			'modelName'=>$this->modelName,
			'attributeName'=>$this->attributeName,
			'attributeDeletedIds'=>$this->attributeDeletedIds,
		]);		
	}

}
