<?php
namespace app\widgets\gallery;

use yii\web\AssetBundle;

class Asset extends AssetBundle
{
	public $sourcePath = '@app/widgets/gallery/assets';
	
	public $css = [
		'css/gallery.css',
	];
	
	public $js = [
    	'js/gallery.js',
    	'js/jquery.iframe-transport.js',
	];
	
	public $depends = [
		//'yii\web\JqueryAsset',
	];
}