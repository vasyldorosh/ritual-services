<?php
namespace app\modules\slider\models;

use Yii;
use yii\helpers\StringHelper;
use yii\data\ActiveDataProvider;

class Slide extends \app\components\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'module_slider_slide';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'slider_id', 'rank'], 'required'],
            [['is_active', 'rank', 'is_button'], 'integer'],
			[['id', 'publish_time', 'url', 'description', 'button_text'], 'safe'],
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
			'slider_id' 	=> 'Слайдер',
			'is_button' 	=> 'Показать кнопку',
			'rank' 			=> 'Ранг',
			'url' 			=> 'Ссылка',
			'content' 		=> 'Описание',
			'button_text' 	=> 'Текст на кнопке',
			'description' 	=> 'Краткое описание',
			'image' 		=> 'Изображение',
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
	
	public function getSlider()
	{
		 return $this->hasOne(Slider::className(), ['id' => 'slider_id']);
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
				'tableName' => "{{%module_slider_slide_lang}}",
				'attributes' => [
					'title', 'content', 'button_text', 'description',
				]
			],	
			'image' => [
				'class' => \app\components\behaviors\ImageUploadBehavior::className(),
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

		$t 	= self::tableName();
		$tl = $t . '_lang';
		
        $query->andFilterWhere([
            $t.'.id' => $this->id,
            $t.'.is_active' => $this->is_active,
            $t.'.slider_id' => $this->slider_id,
            $t.'.is_button' => $this->is_button,
            $t.'.rank' => $this->rank,
        ]);
		$query->joinWith([
			'translation', 
			'slider',
		]);
		
        $query->andFilterWhere(['like', $tl.'.title', $this->title]);
        $query->andFilterWhere(['like', $tl.'.button_text', $this->button_text]);
        $query->andFilterWhere(['like', $tl.'.description', $this->description]);
        $query->andFilterWhere(['like', $t.'.url', $this->url]);
		
        return $dataProvider;
    }	
	
	
}