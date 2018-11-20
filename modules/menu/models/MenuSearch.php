<?php

namespace app\modules\menu\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\menu\models\Menu;

/**
 * MenuSearch represents the model behind the search form about `app\modules\menu\models\Menu`.
 */
class MenuSearch extends Menu
{
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_active', 'type', 'id'], 'integer'],
            [['title'], 'safe'],
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
        $query = self::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);
		
	    $this->load($params);

        $query->andFilterWhere([
            'id' 		=> $this->id,
            'is_active' => $this->is_active,
            'type' 		=> $this->type,
        ]);
		
        $query->andFilterWhere(['like', 'title', $this->title]);
		
        return $dataProvider;
    }
}