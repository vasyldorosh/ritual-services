<?php

namespace app\modules\admin\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * AdminSearch represents the model behind the search form about `app\modules\admin\models\Admin`.
 */
class AdminVsEventLog extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_id', 'event_id', 'action'], 'safe'],
        ];
    }
	
    public static function tableName()
    {
        return '{{%admin_vs_event_log}}';
    }	
	
	public function beforeSave($insert)
	{
		if ($insert) {
			$this->created_at = time();
		}
		
		return parent::beforeSave($insert);
	}
	
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created_at'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'admin_id' => $this->admin_id,
            'event_id' => $this->event_id,
            'action' => $this->action,
        ]);

           
        return $dataProvider;
    }
	
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
			'admin_id' => 'Пользователь',
			'event_id' => 'Событие',
			'action' => 'Действие',
			'created_at' => 'Время привзки/отвязки',
		];
    }	
	
    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }
	
    public function getEvent()
    {
        return $this->hasOne(\app\modules\event\models\Event::className(), ['id' => 'event_id']);
    }
	
	public static function getActionList()
	{
		return [
			'add' => 'add',
			'delete' => 'delete',
		];
	}
    
 }