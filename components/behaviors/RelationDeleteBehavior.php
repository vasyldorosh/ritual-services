<?php
namespace app\components\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;


class RelationDeleteBehavior extends Behavior
{
    public $relations;

	
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
            //ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            //ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
        ];
    }
	
    /**
     * @attach
     */
    public function attach($owner)
    {
        parent::attach($owner);
    }	
	
    public function beforeDelete($event)
    {
		foreach ($this->relations as $table=>$attribute) {
			$this->_deleteRelationItems($table, $attribute);
		}
	}

	
	private function _deleteRelationItems($table, $attribute)
	{
		$sql = "DELETE FROM {$table} WHERE {$attribute} = {$this->owner->id}";
		Yii::$app->db->createCommand($sql)->execute();		
	}
	
}