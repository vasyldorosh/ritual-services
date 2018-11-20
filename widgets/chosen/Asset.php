<?php
namespace app\widgets\chosen;

use yii\web\AssetBundle;

class Asset extends AssetBundle
{
	public $sourcePath = '@app/widgets/chosen/assets';
	
	public $css = [
		'css/chosen.bootstrap.css'
	];
	
	public $js = [
    	'js/chosen.jquery.js',
	];
	
	public $depends = [
		//'yii\web\JqueryAsset',
	];
}