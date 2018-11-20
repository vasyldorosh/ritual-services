<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use app\components\AccessControl;
use app\modules\admin\models\Log;

class IndexController extends \app\components\AdminController {

    public $side = 'module';

    public function actionIndex() {
        $skip = array('.', '..');
		
		$moduleDir = Yii::getAlias('@app') . '/modules/';
		
		$files = scandir($moduleDir);
		
		$modulesData = [];
		
		foreach(\app\helpers\Tree::getModulesRightMenu(true) as $module) {
			$homeFile 	= $moduleDir . $module['id'] . '/home.php';	
			$accessFile = $moduleDir . $module['id'] . '/access.php';	
			
			if (!in_array($module['id'], $skip) && is_file($homeFile) && is_file($accessFile)) {	
				$dataHome 	= include($homeFile);
				$accessData = include($accessFile);
				//d($accessData);
				
				$modulesData[$module['id']]['title'] = $accessData['title'];
				
				foreach ($dataHome as $item) {
					$model = new $item['model'];
				
					if (AccessControl::can($item['action'] . '.index')) {	
			
						$modulesData[$module['id']]['items'][] = [
							'log' 	=> Log::getLastLog($item['action'], $item['model']::getTag()),
							'action'=> $item['action'],
							'title' => $item['title'],
							'data'  => $model->getHomePageData(),
						];
					}
				}	
			}
		}
		
		return $this->render('index', ['modulesData'=>$modulesData]);
    }

}
