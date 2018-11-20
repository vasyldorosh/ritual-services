<?php
namespace app\widgets\vstab;

use yii\web\AssetBundle;

class VstabAsset extends AssetBundle
{
	public $sourcePath = '@app/widgets/vstab/assets';
	
	public $depends = [
		'yii\web\JqueryAsset',
	];
	
	public function init()
	{
		$this->js  = ['js/vstab.js'];
		$this->css  = ['css/vstab.css'];
	}
	
}