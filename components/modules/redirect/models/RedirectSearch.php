<?php

namespace app\modules\redirect\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\redirect\models\Redirect;

class RedirectSearch extends Redirect
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pattern', 'url', 'id'], 'safe'],
            [['rank', 'is_lang', 'is_active'], 'integer'],
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

        $query->andFilterWhere([
            self::tableName() . '.id' => $this->id,
            'is_active' => $this->is_active,
            'is_lang' => $this->is_lang,
            'rank' => $this->rank,
        ]);

        $query->andFilterWhere(['like', 'pattern', $this->pattern]);
        $query->andFilterWhere(['like', 'url', $this->url]);

        return $dataProvider;
    }
}