<?php

namespace app\modules\settings\controllers;

use Yii;
use app\components\AdminController;
use app\modules\settings\models\SettingsModel;
use app\modules\settings\models\Settings;

/**
 * SettingsController implements actions for Event model.
 */
class SettingsController extends AdminController
{
	public $side = '';
	
    public function actionIndex()
    {
		$settings = Yii::$app->request->post('Settings', null);
		
		if (!empty($settings)) {
			SettingsModel::saveData($settings);
			Yii::$app->session->setFlash('success', 'Успешно сохранено');
        }

		$values = Settings::getInstance()->getData();
		
	    return $this->render("index", [
            'values' => $values,
	    ]);		
	}
}
