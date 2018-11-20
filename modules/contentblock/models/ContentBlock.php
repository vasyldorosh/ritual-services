<?php
namespace app\modules\contentblock\models;

use Yii;
use yii\helpers\StringHelper;

class ContentBlock extends \app\components\BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%content_block}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            [['is_active', 'is_not_editor'], 'integer'],
            [['js'], 'safe'],
        ];
    }
	
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = [
            'id' 		=> 'ID',
            'title' 	=> 'Заголовок',
            'content' 	=> 'Содержание',
            'is_active' => 'Активность',
            'created_at'=> 'Создано',
            'image'		=> 'Изображение',
            'js'		=> 'JS',
			'is_not_editor' => 'Не использовать редактор',
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
				'tableName' => "{{%content_block_lang}}",
				'attributes' => [
					'title', 'content',
				]
			],			
			'image' => [
				'class' => \app\components\behaviors\ImageUploadBehavior::className(),
			],						
            'adaptive' => [
                'class'	=>\app\components\imageAdaptive\ImageBehavior::className(),
            ],
            'imageAdaptiveUpload' =>[
                'class'         => \app\components\behaviors\ImageAdaptiveUploadBehavior::className(),
                'attributeName' => \app\components\imageAdaptive\Manager::getAttributes(StringHelper::basename(get_class($this))),
            ],				
		];
	}		

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
		if ($insert) {
			$this->created_at = time();
		}
		
		return parent::beforeSave($insert);
    }	
	
	public static function getList()
	{
		$key 		= self::getTag() . '_getList_';
		$data		= cache()->get($key);
		
		if ($data === false) {
			$data= \yii\helpers\ArrayHelper::map(self::find()->orderBy('id DESC')->all(), 'id', 'title');
			
			cache()->set($key, $data, 0, td(self::getTag()));	
		}
		
		return $data;		
	}	
	
}