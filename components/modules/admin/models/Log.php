<?php

namespace app\modules\admin\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

class Log extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_id', 'action', 'model_id', 'ip'], 'safe'],
        ];
    }
	
    public static function tableName()
    {
        return '{{%admin_log}}';
    }
		
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
			'admin_id' 	=> 'Пользователь',
			'action' 	=> 'Действие',
			'created_at'=> 'Время',
			'ip'		=> 'IP',
			'id'		=> 'ID',
			'model_id'	=> 'ID записи',
		];
    }	
	
    public function getAdmin()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }
	
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
            'sort' => ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);
		
        $this->load($params);

        $query->andFilterWhere([
            'id' 		=> $this->id,
            'model_id' 	=> $this->model_id,
            'admin_id' 	=> $this->admin_id,
        ]);

        $query->andFilterWhere(['like', 'action', $this->action]);
        $query->andFilterWhere(['like', 'ip', $this->ip]);

        return $dataProvider;
    }	
	
	public static function getLastLog($action, $tag)
	{
		$key 	= $tag . 'getLastLog';
		$data 	= Yii::$app->cache->get($key);
		
		if ($data === false) {
			$data 	= [];
			$log 	= self::find()->andWhere("action LIKE '{$action}%'")->joinWith(['admin'])->orderBy('created_at DESC')->one();
			if (!empty($log)) {
				$data = [
					'admin' => $log->admin->email,
					'ip' 	=> $log->ip,
					'time' 	=> $log->created_at,
					'action'=> str_replace($action.'.', '', $log->action),
				];
			}
			
			Yii::$app->cache->set($key, $data, 0, td($tag));
		}
		
		return $data;
	}
	
	    
 }