<?php

namespace app\modules\event\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\event\models\Event;

/**
 * EventSearch represents the model behind the search form about `app\modules\event\models\Event`.
 */
class EventSpoolSearch extends EventSpool
{
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subject', 'content', 'event_id', 'status', 'created_at', 'send_at', 'email_to'], 'safe']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = EventSpool::find();
        
        $dataProvider = new ActiveDataProvider([
            'query'	=> $query,
            'sort' => ['defaultOrder' => ['id'=>SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            self::tableName() . '.id' => $this->id,
            'status' => $this->status,
            'event.id' => $this->event_id,
        ]);
		
		//$query->joinWith(['event.translation']);
		
        $query->andFilterWhere(['like', 'subject', $this->subject]);
        $query->andFilterWhere(['like', 'email_to', $this->email_to]);
        $query->andFilterWhere(['like', 'content', $this->content]);
				
        return $dataProvider;
    }
}