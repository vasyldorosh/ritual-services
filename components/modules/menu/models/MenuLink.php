<?php
namespace app\modules\menu\models;

use Yii;
use yii\db\ActiveRecord;

class MenuLink extends  \app\components\BaseModel
{
    /**
     * @tableName
     */
    public static function tableName()
    {
        return '{{%menu_link}}';
    }
    
    /**
     * @ rules
     */
    public function rules()
    {
        return [
            [['menu_id', 'title'], 'required'],
            [['is_active', 'rank', 'parent_id'], 'integer'],
            [['link', 'class', 'style', 'page_id', 'is_active', 'image', 'description'], 'safe'],
        ];
    }
	
    /**
     * @ labels
     */
    public function attributeLabels()
    {
		$labels = [
			'id' 			=> 	'ID',
            'title' 		=> 'Название',
            'parent_id' 	=> 'Родитель',
            'menu_id' 		=> 'Меню',
            'is_active' 	=> 'Активность',
            'link' 			=> 'Ссылка',
            'page_id' 		=> 'Страница',
            'rank' 			=> 'Порядок',
			'class' 		=> 'HTML класс элемента',
            'style' 		=> 'CSS стиль элемента',
            'image' 		=> 'Изображение',
            'description' 	=> 'Описание',
		];
		
		foreach ($this->behaviors['ml']->attributes as $attr) {
			foreach (Yii::$app->params['otherLanguages'] as $lang=>$langTitle) {
				$langAttr = $attr . '_' . $lang;
				
				if (isset($labels[$attr])) {
					$labels[$langAttr] = $labels[$attr];
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
				'tableName' => "{{%menu_link_lang}}",
				'attributes' => [
					'title', 'description', 
				]
			],			
			'image' => [
				'class' => \app\components\behaviors\ImageUploadBase64Behavior::className(),
			],			
		];
	}	

	public function beforeSave($insert)
	{	
		$this->is_active = (int) $this->is_active;
		
		return parent::beforeSave($insert);
	}
	
}