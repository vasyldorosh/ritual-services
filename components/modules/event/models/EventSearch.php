<?php

namespace app\modules\event\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\event\models\Event;

/**
 * EventSearch represents the model behind the search form about `app\modules\event\models\Event`.
 */
class EventSearch extends Event
{
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_active', 'event_id', 'content_type', 'is_instant'], 'integer'],
            [['subject', 'from_name', 'from_email'], 'safe']
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
        $query = Event::find();
        
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
            'is_active' => $this->is_active,
            'is_instant' => $this->is_instant,
            'content_type' => $this->content_type,
        ]);
		$query->joinWith(['translation']);
		
        $query->andFilterWhere(['like', 'from_email', $this->from_email])
				->andFilterWhere(['like', 'event_lang.from_name', $this->from_name])
				->andFilterWhere(['like', 'event_lang.subject', $this->subject])
				->andFilterWhere(['like', 'event_lang.content', $this->content]);
		
        return $dataProvider;
    }
}