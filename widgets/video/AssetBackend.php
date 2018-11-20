<?php
namespace app\widgets\video;

use yii\web\AssetBundle;

class AssetBackend extends AssetBundle
{
	public $sourcePath = '@app/widgets/video/assets/backend';
	
	public $css = [
		'video.css',
	];
	
	public $js = [
    	'video.js',
	];
	
	public $depends = [
		//'yii\web\JqueryAsset',
	];
}