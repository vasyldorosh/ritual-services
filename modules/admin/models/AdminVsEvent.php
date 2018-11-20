<?php

namespace app\modules\admin\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * AdminSearch represents the model behind the search form about `app\modules\admin\models\Admin`.
 */
class AdminVsEvent extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_id', 'event_id'], 'required'],
        ];
    }
	
    public static function tableName()
    {
        return '{{%admin_vs_event}}';
    }	
    
 }