<?php

namespace app\modules\structure\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\structure\models\Domain;

/**
 * DomainSearch represents the model behind the search form about `app\modules\structure\models\Domain`.
 */
class DomainSearch extends Domain
{
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_active', 'is_root', 'template_id'], 'integer'],
            [['alias', 'lang'], 'safe']
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
            'is_root' => $this->is_root,
            'template_id' => $this->template_id,
        ]);
		
        $query->andFilterWhere(['like', 'alias', $this->alias]);
        $query->andFilterWhere(['like', 'lang', $this->lang]);
		
        return $dataProvider;
    }
}