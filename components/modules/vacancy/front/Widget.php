<?php

namespace app\modules\vacancy\front;

use Yii;
use app\components\BaseWidget;
use app\modules\vacancy\models\Vacancy;
use app\modules\vacancy\models\Country;

class Widget extends BaseWidget
{	
    /**
     * return string
     */
    public function getName()
	{
		return 'Вакансии';
	}
	
	
    /**
     * return array
     */
    public static function getActions()
	{
		if (l() == 'pl') {
			header('Location: /pl');
			return;
		}
		
		
		return [
			'index'   => 'Список вакансий',
			'country' => 'Список вакансий по стране',
			'view'    => 'Просмотр вакансий',
		];
	}
	
    public function actionIndex()
	{
		$items = Vacancy::find()
			->joinWith(['translation', 'country'])
			->where([
				Country::tableName() . '.is_active' => 1,
				Vacancy::tableName() . '.is_active' => 1,
			])
			->orderBy('rank')
			->all();
	
		echo $this->render('index', ['items' => $items]);
	}
	
    public function actionCountry()
	{
		$vars    = $this->getVariables();
		$country = Country::getItemByAlias($vars['alias']);
		
		if (empty($country)) {
			$this->page404();
		}
		
		Yii::$app->controller->title = !empty($country['seo_title']) ? $country['seo_title'] : $country['title'];
		Yii::$app->controller->description = $country['seo_description'];
		Yii::$app->controller->h1 = Yii::$app->controller->title;
		
		$items = Vacancy::find()
			->joinWith(['translation', 'country'])
			->where([
				'country_id' => $country['id'],
				Country::tableName() . '.is_active' => 1,
				Vacancy::tableName() . '.is_active' => 1,
			])
			->orderBy('rank')
			->all();
	
		echo $this->render('country', ['items' => $items]);
	}
    
	public function actionView()
	{
		$vars    = $this->getVariables();
		$country = Country::getItemByAlias($vars['country']);
		
		if (empty($country)) {
			$this->page404();
		}

		$vacancy = Vacancy::getItem($vars['alias'], $country['id']);
		if (empty($vacancy)) {
			$this->page404();
		}

		
		Yii::$app->controller->title = !empty($vacancy['seo_title']) ? $vacancy['seo_title'] : $vacancy['title'];
		Yii::$app->controller->description = $vacancy['seo_description'];
		Yii::$app->controller->h1 = $vacancy['title'];
			
		echo $this->render('view', [
			'vacancy' => $vacancy,
		]);
	}
	
}