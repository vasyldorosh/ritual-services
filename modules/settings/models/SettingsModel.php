<?php
namespace app\modules\settings\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;

class SettingsModel extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%settings}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['key', 'required'],
            ['key', 'unique'],
            ['val', 'safe'],
        ];
    }
   
   public static function getTag()
   {
	   return self::tableName();
   }
	
	public static function getAll()
	{
		$key 		= self::getTag() . '_getAll_';
		$data		= Yii::$app->cache->get($key);
		
		if ($data === false) {
			$data	= ArrayHelper::map(self::find()->all(), 'key', 'val');
			
			Yii::$app->cache->set($key, $data, 0, td(self::getTag()));	
		}
		
		return $data;
	}
	
	public static function saveData($items)
	{
		self::deleteAll();
		
		foreach ($items as $k=>$v) {
			$model = new self;
			$model->key = $k;
			$model->val = $v;
			$model->save();
		}
		
		\yii\caching\TagDependency::invalidate(Yii::$app->cache, self::getTag());
	}	
	
}