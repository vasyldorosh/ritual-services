<?php
namespace app\widgets\video;

use yii\web\AssetBundle;

class AssetFrontend extends AssetBundle
{
	public $sourcePath = '@app/widgets/video/assets/frontend';
	
	public $css = [
		'css/video.css',
	];
	
	public $js = [
    	'js/video.js',
	];
	
	public $depends = [
		//'yii\web\JqueryAsset',
	];
}