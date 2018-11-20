<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\models\Admin;

/**
 * AdminSearch represents the model behind the search form about `app\modules\admin\models\Admin`.
 */
class AdminSearch extends Admin
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_active', 'group_id'], 'integer'],
            [['register_at', 'auth_at'], 'safe'],
            [['name', 'email'], 'safe']
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
            'query' => $query,
            'sort' => ['defaultOrder' => ['id'=>SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            self::tableName() . '.id' => $this->id,
            'group_id' => $this->group_id,
            'is_active' => $this->is_active,
            'FROM_UNIXTIME(register_at, "%d.%m.%Y")' => $this->register_at,
            'FROM_UNIXTIME(auth_at, "%d.%m.%Y")' => $this->auth_at
        ]);

        $query->andFilterWhere(['like', 'email', $this->email])
              ->andFilterWhere(['like', 'name', $this->name]);
           
        return $dataProvider;
    }
}