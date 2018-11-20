<?php
namespace app\modules\slider\models;

use Yii;
use yii\helpers\StringHelper;
use yii\data\ActiveDataProvider;

class Slider extends \app\components\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'module_slider';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['is_active'], 'integer'],
			[['id', 'publish_time'], 'safe'],
        ];
    }
	
	/**
	 * Выполняем ряд действий перед валидацией модели
	 * @return boolean -- результат выполнения операции
	 */
	public function beforeValidate()
	{	
		if (!empty($this->publish_time) && !is_numeric($this->publish_time)) {
			$this->publish_time = strtotime($this->publish_time);
		} 

		if (empty($this->publish_time)) {
			$this->publish_time = time();
		}
				
		return parent::beforeValidate();
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
			'publish_time' 	=> 'Дата публикации',
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
	
	public static function find()
    {
        return new \app\components\multilingual\MultilingualQuery(get_called_class());
    }	
	
	public function behaviors()
	{
		return [
			'ml' => [
				'class' => \app\components\multilingual\MultilingualBehavior::className(),
				'tableName' => "{{%module_slider_lang}}",
				'attributes' => [
					'title',
				]
			],			
		];
	}		

 	public static function getList()
	{
		 return \yii\helpers\ArrayHelper::map(self::find()->localized()->all(), 'id', 'title');
	}
	
    public function search($params)
    {
        $query = self::find();
        
        $dataProvider = new ActiveDataProvider([
            'query'	=> $query,
            'sort' => ['defaultOrder' => ['id'=>SORT_DESC]],
        ]);

        $this->load($params);

        $query->andFilterWhere([
            self::tableName() . '.id' => $this->id,
            'is_active' => $this->is_active,
        ]);
		$query->joinWith(['translation']);
		
		$tl = self::tableName() . '_lang';

        $query->andFilterWhere(['like', $tl.'.title', $this->title]);
		
        return $dataProvider;
    }	
	
	public static function getCacheSlider($slider_id)
	{
		$key = self::getTag() . 'getCacheSlider' . $slider_id;
		$expire = cache()->get($key);
		$time = time();
		if ($expire === false) {
		
			$sql = "SELECT MIN(publish_time) FROM ".Slide::tableName()." WHERE publish_time >={$time} AND is_active = 1 AND slider_id = {$slider_id}";
							
			$data = (int)db()->createCommand($sql)->queryScalar();
			$expire = $data-$time;
			if ($expire < 0) $expire=0;
			
			cache()->set($key, $expire, $expire, td(self::getTag()));
		}	

		return $expire;
	}	
	
	public static function getSlides($slider_id)
	{
		$key 	= Slider::getTag() . 'getSlides' . $slider_id . l();
		$data 	= cache()->get($key);
		if ($data === false) {
			
			$data = [];
			
			$time  = time();
			
			$items = Slide::find()->where(['slider_id'=>$slider_id, 'is_active'=>1])->andWhere("publish_time < {$time}")->orderBy('rank')->all();
			
			foreach ($items as $item) {
				$data[] = array(
					'id' 			=> $item->id,
					'image' 		=> $item->getImageUrl('image'),
					'title' 		=> $item->title,
					'is_button' 	=> $item->is_button,
					'content' 		=> $item->content,
					'button_text' 	=> $item->button_text,
					'description' 	=> $item->description,
					'url' 			=> str_replace('{lang}', l(), $item->url),
				);
			}
			
			cache()->set($key, $data, self::getCacheSlider($slider_id), td([Slider::getTag(), Slide::getTag()]));
		}

		return $data;
	}
	
	public static function getItem($slider_id)
	{
		$slider_id 	= (int) $slider_id;
		$key 		= Slider::getTag() . 'getItem' . $slider_id . l();
		$data 		= cache()->get($key);
		
		if ($data === false) {
			
			$data = [];
			
			$time = time();
			
			$item = Slider::find()->where(['id'=>$slider_id])->andWhere("publish_time < {$time}")->one();
			
			if (!empty($item)) {
				$data['slider'] = array(
					'id' => $item->id,
					'title' => $item->title,
				);
				
				cache()->set($key, $data, 0, td(Slider::getTag()));
			}
		}
		
		return $data;
	}
		
}