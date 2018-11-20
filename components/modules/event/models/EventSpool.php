<?php
namespace app\modules\event\models;

use Yii;
use yii\data\ActiveDataProvider;

class EventSpool extends \app\components\BaseModel
{
	public $post_files = [];
	
	
    /**
     * @table name
     */
    public static function tableName()
    {
        return '{{%event_spool}}';
    }
    
    /**
     * @validation rules
     */
    public function rules()
    {
        return [
            [['subject', 'content', 'event_id'], 'required'],
            [['post_files', 'status'], 'safe'],
        ];
    }
	
    /**
     * @attribute labels
     */
    public function attributeLabels()
    {
        $labels = [
            'id' 		=> 'ID',
            'subject' 	=> 'Тема',
            'content' 	=> 'Содержание',
            'event_id' 	=> 'Шаблон письма',
            'status' 	=> 'Статус',
            'created_at'=> 'Создано',
            'send_at'	=> 'Отправлено',
            'email_to'	=> 'Получатель',
         ];
		
		return $labels;
    }
	
    /**
     * @actions before validation
     */
    public function beforeSave($insert)
    {
		if ($insert) {
			$this->created_at = time();
			$this->status = \app\components\Event::SPOOL_STATUS_AWAIT;
		}
		
		return parent::beforeSave($insert);
    }	
	
	public function getEvent()
	{
		return $this->hasOne(\app\modules\event\models\Event::className(), ['id' => 'event_id']);
	}	
	
	public function getHomePageData()
	{
		$key 	= self::getTag() . '_getHomePageData_';
		$data 	= Yii::$app->cache->get($key);
		
		if ($data === false) {
			$data = [];
			$data['Всего элементов'] = self::find()->count();
			foreach (\app\components\Event::getListSpoolStatus() as $status=>$title) {
				$data[$title] = self::find()->where(['status'=>$status])->count();			
			}
			
			Yii::$app->cache->set($key, $data, 0, td([self::getTag()]));
		}
		
		return $data;
	}	
	
	
}