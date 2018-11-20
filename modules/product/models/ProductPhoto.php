<?php
namespace app\modules\product\models;

use Yii;

class ProductPhoto extends \app\components\BaseModel
{
    
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'module_product_photo';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id'], 'required'],
			[['id', 'rank', ], 'integer'],
            [['title', 'image', 'title'], 'safe'],
		];
    }
	
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = [
			'title' 	=> 'Название',
			'image' 	=> 'Изображение',
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
				'tableName' => 'module_product_photo_lang',
				'attributes' => [
					'title',
				]
			],				
			'image' => [
				'class' => \app\components\behaviors\ImageUploadBase64Behavior::className(),
			],		
        ];
    }	
    
    public function beforeSave($insert) 
    {
                
        if ($insert) {		
            $sql 	= "SELECT MAX(rank) AS rank FROM ".self::tableName()." WHERE product_id={$this->product_id}";
            $rank 	= Yii::$app->db->createCommand($sql)->queryScalar();
            $this->rank = $rank+1;
        }
        
        return parent::beforeSave($insert);
    }
    
    public function getDataTags() 
    {
        return [self::getTag() . '_product_id_' . $this->product_id];
    }
	
    public function getTitleList() 
    {
        return $this->title;
    }
	
    public function getDataInfo() 
    {
		$base64 = '';
		$file   = Yii::getAlias('@webroot') . $this->getImageUrl('image');
		if (is_file($file)) {		
			$type = pathinfo($file, PATHINFO_EXTENSION);
			$data = file_get_contents($file);
			$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);		
		}

		$data = [
			'id' 		    => $this->id,
			'title'     	=> $this->title,
			'rank'     	    => $this->rank,
            'preview' 	    => $this->getImageUrl('image', 480, null, 'resize'),
			'image' 		=> $this->getImageUrl('image', 480, null, 'resize'),			
			'image_base64' 	=> $base64,			
		];
		
		foreach ($this->behaviors['ml']->attributes as $attr) {
			foreach (Yii::$app->params['otherLanguages'] as $lang=>$langTitle) {
				$langAttr = $attr . '_' . $lang;
				
				$data[$langAttr] = $this->$langAttr;				 
			}
		}		
		
        return $data;
    }
	
	
    
        
}
