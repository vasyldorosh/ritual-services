<?php
namespace app\modules\admin\models;

use Yii;
use yii\helpers\ArrayHelper;

class Group extends \app\components\BaseModel
{
	public $post_access;
		
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_group}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['title', 'required'],
            [['is_active', 'is_super'], 'integer'],
            ['post_access', 'safe'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' 		=> 'ID',
            'title' 	=> 'Название',
            'is_active' => 'Активность',
            'is_super' 	=> 'Супер админ',
        ];
    }
	
    public function afterValidate()
    {
		if (!empty($this->post_access)) {
			$this->access = serialize($this->post_access);
		}
		
        return parent::afterValidate();
    }	
    
    public static function getList()
    {
        return ArrayHelper::map(self::find()->all(),'id','title');
    }
	
	public function getSelectedAccess()
	{
		if (Yii::$app->request->isPost) {
			
			return $this->post_access;
			
		} else {
			
			$data = @unserialize($this->access);
			
			return is_array($data) ? $data: [];
		}
	}
	
	public static function getAccessListByGroup($group_id)
	{
		$group_id	= (int) $group_id;
		$key 		= self::getTag() . '_getAccessListByGroup__' . $group_id;
		$data		= Yii::$app->cache->get($key);
		
		if ($data === false) {
			$data	= [];
			$model	= self::findOne($group_id);
			if (!empty($model)) {
				$data 	= unserialize($model->access);
			}
			
			Yii::$app->cache->set($key, $data, 0, td(self::getTag()));	
		}
		
		return $data;
	}
	
}