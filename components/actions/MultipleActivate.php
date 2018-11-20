<?php 
namespace app\components\actions;

use Yii;
use yii\web\NotFoundHttpException;

class MultipleActivate extends \yii\base\Action
{
    public $model;
    
    public function run()
    {
		$response['success'] = 1;
		
        $model = $this->model;
        $ids = (array)Yii::$app->request->getQueryParam('ids');
		
		if (!empty($ids)) {
			$action = Yii::$app->controller->module->id . '.' . Yii::$app->controller->id . '.activate';
			foreach ($ids as $id) {
				$m = $model::findOne($id);
				$m->is_active = 1;
				$m->save();
				
				\app\components\Log::save($id, $action);
			}
		}	
			
        return json_encode($response);
    }
} 
?>