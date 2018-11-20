<?php
namespace app\widgets\video;

use Yii;
use yii\validators\Validator;
use yii\db\ActiveRecord;

class Behavior extends \yii\base\Behavior
{
	public $classModelVsVideo = null;
	public $attribute = null;
	public $activity  = false;
	public $post_videos;
	public $deleted_videos_ids;
	
	
    /**
     * @events
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
        ];
    }

    /**
     * @attach
     */
    public function attach($owner)
    {
		$owner->validators[] = Validator::createValidator('safe', $owner, 'post_videos', []);		
		$owner->validators[] = Validator::createValidator('safe', $owner, 'deleted_videos_ids', []);		
		
        parent::attach($owner);
    }
	
    public function afterSave($event)
    {
		if (!empty($this->post_videos)) {
			$uploadedVideoIds = [];
			foreach ($this->post_videos as $videoUrl) {
				$model = new $this->classModelVsVideo;
				$model->{$this->attribute} = $this->owner->id;
				$model->image = \app\helpers\Video::getImageByUrl($videoUrl);
				$model->url = $videoUrl;
				if ($model->save()) {
					$uploadedVideoIds[] = $model->id;
				}
			}
			
			if ($this->activity) {
				if ($this->owner->getActive() && !empty($uploadedVideoIds)) {
					$activity = new \app\modules\user\models\Activity;
					$activity->user_id 		= $this->owner->user_id;
					$activity->type 		= \app\modules\user\models\Activity::T_BLOG_UPLOAD_VIDEO;
					$activity->main_model_id= $this->owner->id;
					$activity->url 			= $this->owner->url;
					$activity->data 		= ['title'=>$this->owner->getTitleData(), 'count'=>count($uploadedVideoIds), 'ids'=>$uploadedVideoIds];
					$activity->save();					
				}
			}
		}
		
		if (!empty($this->deleted_videos_ids)) {
			$this->deleteVideos(explode(',', $this->deleted_videos_ids));
		}		
    }

	private function deleteVideos($ids=[])
	{
		$where = [];
		$where[$this->attribute] = $this->owner->id;
		if (!empty($ids) && is_array($ids)) {
			$where['id'] = $ids;
		}

		$modelName = $this->classModelVsVideo;
		
		$items = $modelName::find()->where($where)->all();

		foreach ($items as $item) {
			$item->delete();
		}		
	}

}
