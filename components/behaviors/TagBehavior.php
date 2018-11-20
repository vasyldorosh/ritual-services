<?php
namespace app\components\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\Query;
use app\modules\tag\models\Tag;


class TagBehavior extends Behavior
{
	public $current_tag;
	public $attributeName;
	public $attributeTag;
	
	
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
        ];
    }
	
    /**
     * @attach
     */
    public function attach($owner)
    {
        parent::attach($owner);
    }	

    public function afterFind($event)
    {
		$r = Yii::$app->request->get('r');
		
		if (!empty($r))
			$this->current_tag = $this->_getTags();
    } 
	
    public function afterInsert($event)
    {
		$this->afterSave(true);
	}	
		
	public function afterUpdate($event)
    {
		$this->afterSave(false);
	}	
		
    public function afterSave($insert)
    {
		$postTag = $this->getPost_tag();
		
		if (!$insert && $this->current_tag != $postTag) {
			$sql = "DELETE FROM {$this->tableName}_vs_tag WHERE {$this->attributeName} = {$this->owner->id}";
			Yii::$app->db->createCommand($sql)->execute();	
		}
		
		if (!empty($postTag) && $this->current_tag != $postTag)
		{
			$tags = explode(Tag::SEPARATE, $postTag);
			
			if (is_array($tags) && empty($tags) == false)
			{
				$data = [];
				foreach ($tags as $tag) 
				{
					if (trim($tag) != '')
					{
						$model = Tag::getTagByTitle($tag);
						$data[] = [
							$this->attributeName => $this->owner->id,
							'tag_id' => $model->id,
						];
					}
				}
				
				if (!empty($data))
					Yii::$app->db->createCommand()->batchInsert("{$this->tableName}_vs_tag", [$this->attributeName, 'tag_id'] , $data)->execute();
			}
		}
    } 
	
    public function getTag() {
       if (!empty($this->owner->id)) {
			$tag = array();

			return $this->_getTags();
        } else {
			return false;
        }
    }	
	
	public function getTableName() {
		$class = get_class($this->owner);
		return $class::tableName();
	}
	
	
	private function _getTags()
	{
		$ownerId = (int) $this->owner->id;
		
		$query = new Query;
		$items = $query->select('tag_lang.title')
			->from("{$this->tableName}_vs_tag AS vs")
			->where("vs.{$this->attributeName} = {$ownerId}")
			->join('LEFT JOIN', 'module_tag AS tag', 'vs.tag_id = tag.id')
			->join('LEFT JOIN', 'module_tag_lang AS tag_lang', 'tag.id = tag_lang.owner_id AND tag_lang.lang = "'.Yii::$app->sourceLanguage.'"')
			->all();
		
		$tags = [];
        foreach ($items as $item) {
            $tags[] = $item['title'];
        }
		
		
		return implode(', ', $tags);
	}

    public function setTag($value) {
        $this->tag = $value;
    }

    public function getTags_title() {
		if (r()->isPost){
			return $this->getPost_tag();
		}        		

		return $this->_getTags();
	}	
 
    public function getPost_tag() {
		$post = r()->post(\yii\helpers\StringHelper::basename(get_class($this->owner)));
			
		if (isset($post[$this->attributeTag]))
			return $post[$this->attributeTag];
		
		return '';
	}	
 
	
}