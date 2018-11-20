<?php
namespace app\modules\vacancy\models;

use Yii;
use yii\helpers\StringHelper;
use yii\data\ActiveDataProvider;

class Vacancy extends \app\components\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'module_vacancy';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'country_id', 'rank', 'content'], 'required'],
            [['is_active', 'rank', ], 'integer'],
            [['alias', ], 'unique'],
			[['id', 'create_time', 'seo_description', 'seo_title', 'work_time', 'salary', 'alias'], 'safe'],
        ];
    }
	
	/**
	 * Выполняем ряд действий перед валидацией модели
	 * @return boolean -- результат выполнения операции
	 */
	public function beforeValidate()
	{	
		if (empty($this->create_time)) {
			$this->create_time = time();
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
			'create_time' 	=> 'Дата создания',
			'country_id' 	=> 'Страна',
			'rank' 			=> 'Ранг',
			'content' 		=> 'Описание',
			'seo_title' 	=> 'Seo Title',
			'seo_description' => 'Seo Description',
			'image' 		=> 'Изображение',
			'work_time' 	=> 'График роботы',
			'salary' 	=> 'Зарплата',
			'alias' 	=> 'Алиас',
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
	
	public function getCountry()
	{
		 return $this->hasOne(Country::className(), ['id' => 'country_id']);
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
				'tableName' => "module_vacancy_lang",
				'attributes' => [
					'title', 'content', 'seo_title', 'seo_description', 'salary', 'work_time'
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
            $t.'.country_id' => $this->country_id,
            $t.'.rank' => $this->rank,
        ]);
		$query->joinWith([
			'translation', 
			'country',
		]);
		
        $query->andFilterWhere(['like', $tl.'.title', $this->title]);
        $query->andFilterWhere(['like', $tl.'.seo_description', $this->seo_description]);
        $query->andFilterWhere(['like', $tl.'.seo_title', $this->seo_title]);
        $query->andFilterWhere(['like', $tl.'.salary', $this->salary]);
        $query->andFilterWhere(['like', $tl.'.work_time', $this->work_time]);
        $query->andFilterWhere(['like', $tl.'.alias', $this->alias]);
		
        return $dataProvider;
    }	
	
	public static function getItem($alias, $country_id)
	{
		$key 		= Country::getTag() . '_getItem_' . $alias . '_' . $country_id . l();
		$data 		= cache()->get($key);
		
		if ($data === false) {
			
			$data = [];
			
			$time = time();
			
			$item = self::find()
					->joinWith(['country'])
					->where([
						Vacancy::tableName() . '.alias'=>$alias,
						Vacancy::tableName() . '.country_id'=>$country_id,
						Vacancy::tableName() . '.is_active'=>1,
						Country::tableName() . '.is_active'=>1,					
					])
					->one();
			
			if (!empty($item)) {
				$data = array(
					'id' 		=> $item->id,
					'title' 	=> $item->title,
					'content' 	=> $item->content,
					'seo_description' => $item->seo_description,
					'seo_title' => $item->seo_title,
					'work_time' => $item->work_time,
					'salary' => $item->salary,
					'image' => $item->getImageUrl('image'),
				);
				
				cache()->set($key, $data, 0, td([Country::getTag(), self::getTag()]));
			}
		}
		
		return $data;
	}
	
	
	public function getUrl()
	{
		return sprintf('/%/vacancy/%s/%s', l(), $this->country->alias, $this->alias);
	}
	
	public function getDescription()
	{
		return \app\helpers\String::truncate(strip_tags($this->content), 500);
	}
	
	
}