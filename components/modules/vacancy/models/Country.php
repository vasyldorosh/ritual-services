<?php
namespace app\modules\vacancy\models;

use Yii;
use yii\helpers\StringHelper;
use yii\data\ActiveDataProvider;

class Country extends \app\components\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'module_vacancy_country';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'rank'], 'required'],
            [['is_active', 'rank'], 'integer'],
            [['alias'], 'unique'],
			[['id', 'seo_title', 'seo_description', 'alias'], 'safe'],
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
            'alias' 		=> 'Алиас',
			'rank' 			=> 'Ранг',
			'seo_title' 	=> 'Seo Title',
			'seo_description' 	=> 'Seo Description',
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
				'tableName' => "{{%module_vacancy_country_lang}}",
				'attributes' => [
					'title', 'seo_description', 'seo_title',
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
            'rank' => $this->rank,
        ]);
		$query->joinWith(['translation']);
		
		$tl = self::tableName() . '_lang';

        $query->andFilterWhere(['like', $tl.'.title', $this->title]);
        $query->andFilterWhere(['like', $tl.'.alias', $this->alias]);
        $query->andFilterWhere(['like', $tl.'.seo_title', $this->seo_title]);
        $query->andFilterWhere(['like', $tl.'.seo_description', $this->seo_description]);
		
        return $dataProvider;
    }	
		
	public static function getItemByAlias($alias)
	{
		$key 		= Country::getTag() . 'getItemByAlias' . $alias . l();
		$data 		= cache()->get($key);
		
		if ($data === false) {
			
			$data = [];
			
			$time = time();
			
			$item = Country::find()->where(['alias'=>$alias])->andWhere("is_active = 1")->one();
			
			if (!empty($item)) {
				$data = array(
					'id' => $item->id,
					'title' => $item->title,
					'seo_description' => $item->seo_description,
					'seo_title' => $item->seo_title,
				);
				
				cache()->set($key, $data, 0, td(Country::getTag()));
			}
		}
		
		return $data;
	}
		
	public static function getItems()
	{
		$key 		= Country::getTag() . 'getItems' . l();
		$data 		= cache()->get($key);
		
		if ($data === false) {
			
			$data = [];
			
			$ids = db()->createCommand("SELECT DISTINCT country_id FROM module_vacancy WHERE is_active=1")->queryColumn();
			
			if (!empty($ids)) {
				$items = Country::find()->where(['is_active'=>1, 'id'=>$ids])->orderBy('rank')->all();
				
				foreach ($items as $item) {
					$data[] = array(
						'id' => $item->id,
						'title' => $item->title,
						'url' => $item->url,
					);				
				}
			}
			
			cache()->set($key, $data, 0, td([self::getTag(), Vacancy::getTag()]));

		}
		
		return $data;
	}
	
	public function getUrl()
	{
		return sprintf('/%/vacancy/%s', l(), $this->alias);
	}
	
		
}