<?php

namespace app\modules\contentblock\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\contentblock\models\ContentBlock;

/**
 * ContentBlockSearch represents the model behind the search form about `app\modules\contentblock\models\ContentBlock`.
 */
class ContentBlockSearch extends ContentBlock
{	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_active', 'is_not_editor', 'title', 'content'], 'safe'],
        ];
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
        ]);
		$query->joinWith(['translation']);

        $query->andFilterWhere(['like', 'content_block_lang.title', $this->title])
				->andFilterWhere(['like', 'content_block_lang.content', $this->content]);
		
        return $dataProvider;
    }
}