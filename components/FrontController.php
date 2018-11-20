<?php

namespace app\components;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;

class FrontController extends Controller
{
	public function beforeAction($action)
	{
		$lang = r()->get('lang');
		if (!empty($lang)) {
			Yii::$app->language = $lang;
			
			$session = new \yii\web\Session;
			$session->open();
			$session['lang'] = $lang;							
		}		
        
		return parent::beforeAction($action);
    }	
}