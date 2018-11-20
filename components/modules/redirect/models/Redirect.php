<?php
namespace app\modules\redirect\models;

use Yii;
use yii\db\ActiveRecord;

class Redirect extends \app\components\BaseModel
{	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'redirect';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pattern', 'url', 'rank'], 'required'],
            [['rank', 'is_lang', 'is_active'], 'integer'],
        ];
    }
	
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = [
         	'pattern' 	=> 'Ссылка от куда перенаправляем',
			'url' 		=> 'Ссылка куда перенаправляем',
			'is_lang' 	=> 'Мультиязычность',
			'is_active' => 'Активность',
			'rank'		=> 'Ранг',			
        ];
		
		return $labels;
    }
		
	public static function getAll()
	{
		$key  = self::getTag() . '_getAll_';
		$data = Yii::$app->cache->get($key);
		
		if ($data === false) {	
			$data = [];
			$items = self::find()->where('is_active = 1')->all();
			foreach($items as $item){
				if($item->is_lang){
					$data['/^(.{2})\\/'.str_replace("/","\\/",trim($item->pattern,'/')).'$/']=array('vars'=>'lang','path'=>'/:lang'.$item->url);
				} else {
					$data['/^'.str_replace("/","\\/",trim($item->pattern,'/')).'$/']=array('path'=>$item->url);
				}
			}			
			
			Yii::$app->cache->set($key, $data, 0, td(self::getTag()));	
		}
		
		return $data;
	}	
}