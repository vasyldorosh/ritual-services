<?php

namespace app\modules\product\front;

use Yii;
use app\components\BaseWidget;
use app\modules\product\models\Product;
use app\modules\product\models\Category;

class Widget extends BaseWidget
{	
    /**
     * return string
     */
    public function getName()
	{
		return 'Товары';
	}
	
	
    /**
     * return array
     */
    public static function getActions()
	{
		return [
			'index'   => 'Список товаров',
			'category' => 'Список товаров по категории',
			'view'    => 'Просмотр товара',
		];
	}
	
    public function actionIndex()
	{
		$items = Product::find()
			->joinWith(['translation', 'category'])
			->where([
				Category::tableName() . '.is_active' => 1,
				Product::tableName() . '.is_active' => 1,
			])
			->orderBy('rank')
			->all();
        
        
        Yii::$app->controller->activeWidgetData['menu'] = Category::getItems();
        
		echo $this->render('index', ['items' => $items]);
	}
	
    public function actionCategory()
	{
		$vars    = $this->getVariables();
		$category = Category::getItemByAlias($vars['alias']);
		
		if (empty($category)) {
			$this->page404();
		}
		
		Yii::$app->controller->title = !empty($category['seo_title']) ? $category['seo_title'] : $category['title'];
		Yii::$app->controller->description = $category['seo_description'];
		Yii::$app->controller->h1 = Yii::$app->controller->title;
		
		$items = Product::find()
			->joinWith(['translation', 'category'])
			->where([
				'category_id' => $category['id'],
				Category::tableName() . '.is_active' => 1,
				Product::tableName() . '.is_active' => 1,
			])
			->orderBy('rank')
			->all();
        
        Yii::$app->controller->activeWidgetData['menu'] = Category::getItems();
        Yii::$app->controller->activeWidgetData['menu_active_url'] = $category['url'];

        
		echo $this->render('category', ['items' => $items]);
	}
    
	public function actionView()
	{
		$vars    = $this->getVariables();
		$category = Category::getItemByAlias($vars['category']);
		
		if (empty($category)) {
			$this->page404();
		}

		$product = Product::getItem($vars['alias'], $category['id']);
		if (empty($product)) {
			$this->page404();
		}

		
		Yii::$app->controller->title = !empty($product['seo_title']) ? $product['seo_title'] : $product['title'];
		Yii::$app->controller->description = $product['seo_description'];
		Yii::$app->controller->h1 = $product['title'];
			
        Yii::$app->controller->activeWidgetData['menu'] = Category::getItems();
        Yii::$app->controller->activeWidgetData['menu_active_url'] = $category['url'];

        $photos = Product::getFrontPhotos($product['id']);
        
        if (!empty($product['image_1200x760'])) {
            array_unshift($photos, [
                'title'             => $product['title'],
                'image_1200x760'    => $product['image_1200x760'],
                'image_360x230'     => $product['image_360x230'],
            ]);
        }
        
		echo $this->render('view', [
			'product' => $product,
			'photos'  => $photos,
		]);
	}
	
}