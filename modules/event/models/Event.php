<?php
namespace app\modules\event\models;

use Yii;

class Event extends \app\components\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%event}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subject', 'content', 'event_id', 'content_type'], 'required'],
            [['event_id'], 'unique', 'message' => 'Это событие уже создано.'],
            [['from_email'], 'email'],
            [['is_active', 'is_instant'], 'integer'],
            [['from_name'], 'safe'],
        ];
    }
	
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = [
            'id' => 'ID',
            'subject' => 'Тема',
            'content' => 'Содержание',
            'event_id' => 'Ид события',
            'is_active' => 'Активность',
            'is_instant' => 'Мгновенная отправка',
            'from_name' => 'Письмо от (Имя)',
            'from_email' => 'Письмо от (E-mail)',
            'content_type' => 'Контент-тип',
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
				'tableName' => "{{%event_lang}}",
				'attributes' => [
					'subject', 'from_name', 'content',
				]
			],			
		];
	}		

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {

		return parent::beforeSave($insert);
    }	
	
	public static function getList()
	{
		 return \yii\helpers\ArrayHelper::map(self::find()->all(), 'id', 'subject');
	}
	
	public static function getData()
	{
		 $data = [];
		 
		 $items = self::find()->with('translations')->asArray()->indexBy('id')->all();
		 
		 
		 foreach ($items as $id=>$item) {
			$translations = $item['translations'];
			unset($item['translations']);
		    $data[$id] = $item;
			
			foreach ($translations as $translation) {
				$data[$id]['translations'][$translation['lang']] = $translation;
			}
				
		 }
		 
		 return $data;
	}
	
}