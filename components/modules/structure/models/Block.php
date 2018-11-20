<?php
namespace app\modules\structure\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

class Block extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%structure_page_block}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['alias', 'page_id', 'content', 'type_id'], 'required'],
        ];
    }

	public function getPage()
	{
		return $this->hasOne(Page::className(), ['id' => 'page_id']);
	}
	
	public function _clerCache()
	{
		
	}
	
    /**
     * Handle 'afterUpdate' event of the owner.
     */
    public function afterSave($insert, $changedAttributes)
    {
		$this->_clearCache();
		
		return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Handle 'afterDelete' event of the owner.
     */
    public function afterDelete()
    {
		$this->_clearCache();
    }

	private function _clearCache()
	{
		\yii\caching\TagDependency::invalidate(cache(), Page::getTag() . '_' . $this->page_id);
	}	
	
}