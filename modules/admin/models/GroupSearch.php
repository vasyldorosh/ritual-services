<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\Group;

/**
 * GroupSearch represents the model behind the search form about `app\modules\admin\models\Group`.
 */
class GroupSearch extends Group
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_active', 'is_super'], 'integer'],
            [['title'], 'safe']
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
            'query'	=> $query,
            'sort'	=> ['defaultOrder' => ['id'=>SORT_DESC]],
        ]);

        $this->load($params);

        $query->andFilterWhere([
            'id' 		=> $this->id,
            'is_active' => $this->is_active,
            'is_super' 	=> $this->is_super,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
}