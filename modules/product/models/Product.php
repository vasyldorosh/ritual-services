<?php
namespace app\modules\product\models;

use Yii;
use yii\helpers\StringHelper;
use yii\data\ActiveDataProvider;

class Product extends \app\components\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'module_product';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'category_id', 'rank', 'content', 'price'], 'required'],
            [['is_active', 'rank', 'price'], 'integer'],
            [['alias', ], 'unique'],
			[['id', 'seo_description', 'seo_title', 'price', 'alias'], 'safe'],
        ];
    }
	
	
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = [
            'id' 			=> 'ID',
            'title' 		=> 'Название',
            'is_active' 	=> 'Активность',
			'category_id' 	=> 'Категория',
			'rank' 			=> 'Ранг',
			'content' 		=> 'Описание',
			'seo_title' 	=> 'Seo Title',
			'seo_description' => 'Seo Description',
			'image' 		=> 'Изображение',
			'price' 	    => 'Цена',
			'alias' 	    => 'Алиас',
         ];
		
		foreach ($this->behaviors['ml']->attributes as $attr) {
			foreach (Yii::$app->params['otherLanguages'] as $lang=>$langTitle) {
				$langAttr = $attr . '_' . $lang;
				
				if (isset($labels[$attr])) {
					$labels[$langAttr] = $labels[$attr] . " ({$lang})";
				} 
			}
		}
		
		return $labels;
    }
	
	public function getCategory()
	{
		 return $this->hasOne(Category::className(), ['id' => 'category_id']);
	}		
	
	public static function find()
    {
        return new \app\components\multilingual\MultilingualQuery(get_called_class());
    }	
	
	public function behaviors()
	{
		return [
			'ml' => [
				'class' => \app\components\multilingual\MultilingualBehavior::className(),
				'tableName' => "module_product_lang",
				'attributes' => [
					'title', 'content', 'seo_title', 'seo_description',
				]
			],	
			'image' => [
				'class' => \app\components\behaviors\ImageUploadBehavior::className(),
			],						
		];
	}		

    public function search($params)
    {
        $query = self::find();
        
        $dataProvider = new ActiveDataProvider([
            'query'	=> $query,
            'sort' => ['defaultOrder' => ['id'=>SORT_DESC]],
        ]);

        $this->load($params);

		$t 	= self::tableName();
		$tl = $t . '_lang';
		
        $query->andFilterWhere([
            $t.'.id' => $this->id,
            $t.'.is_active' => $this->is_active,
            $t.'.category_id' => $this->category_id,
            $t.'.rank' => $this->rank,
            $t.'.price' => $this->price,
        ]);
		$query->joinWith([
			'translation', 
			'category',
		]);
		
        $query->andFilterWhere(['like', $tl.'.title', $this->title]);
        $query->andFilterWhere(['like', $tl.'.seo_description', $this->seo_description]);
        $query->andFilterWhere(['like', $tl.'.seo_title', $this->seo_title]);
        $query->andFilterWhere(['like', $tl.'.alias', $this->alias]);
		
        return $dataProvider;
    }	
	
	public static function getItem($alias, $category_id)
	{
		$key 		= Category::getTag() . '_getItem_' . $alias . '_' . $category_id . l();
		$data 		= cache()->get($key);
		
		if ($data === false) {
			
			$data = [];
			
			$time = time();
			
			$item = self::find()
					->joinWith(['category'])
					->where([
						Product::tableName() . '.alias'=>$alias,
						Product::tableName() . '.category_id'=>$category_id,
						Product::tableName() . '.is_active'=>1,
						Category::tableName() . '.is_active'=>1,					
					])
					->one();
			
			if (!empty($item)) {
				$data = array(
					'id' 		        => $item->id,
					'title' 	        => $item->title,
					'content' 	        => $item->content,
					'seo_description'   => $item->seo_description,
					'seo_title'         => $item->seo_title,
					'price'             => $item->price,
					'image_1200x760'    => $item->getImageUrl('image', 1200, 760, 'resize'),
					'image_360x230'     => $item->getImageUrl('image', 360, 230, 'crop'),
				);
				
				cache()->set($key, $data, 0, td([Category::getTag(), self::getTag()]));
			}
		}
		
		return $data;
	}
	
	
	public function getUrl()
	{
		return d_l('/catalog/'. $this->category->alias . '/' . $this->alias);
	}
	
	public function getDescription()
	{
		return \app\helpers\String::truncate(strip_tags($this->content), 420);
	}
	
    public function getPhotos()
    {
        return  ProductPhoto::find()->where(['product_id' => $this->id])->orderBy('rank')->all();
    }
    
	public static function getFrontPhotos($product_id)
	{
		$product_id  = (int) $product_id;
		
		$tag = ProductPhoto::getTag() . '_product_id_' . $product_id;
		
		$key = ProductPhoto::getTag() . '_getFrontPhotos' . $product_id . l();
		$data= cache()->get($key);
		
		if ($data === false) {
			$data = [];
			$items = ProductPhoto::find()->where(['product_id'=>$product_id])->orderBy('rank')->all();
			
			foreach ($items as $item) {
				$data[] = [
					'id' 			=> $item->id,
					'title' 		=> $item->title,
					'image_1200x760'=> $item->getImageUrl('image', 1200, 760, 'resize'),
					'image_360x230' => $item->getImageUrl('image', 360, 230, 'crop'),
				];
			}
			
            
			cache()->set($key, $data, 0, td([$tag, ProductPhoto::getTag()]));
		}
		
		return $data;
	}
    
    
	
}