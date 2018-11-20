<?php 
namespace app\components\actions;

use Yii;
use yii\web\NotFoundHttpException;

class MultipleColumn extends \yii\base\Action
{
    public $model;
    
    public function run()
    {
		$response['success'] = 1;
		
        $model = $this->model;
        $ids 		= (array)Yii::$app->request->getQueryParam('ids');
		$attribute 	= r()->get('attribute');
		$value 		= r()->get('value');
		
		if (!empty($ids)) {
			$action = Yii::$app->controller->module->id . '.' . Yii::$app->controller->id . '.update';
			foreach ($ids as $id) {
				$m = $model::findOne($id);
				$m->$attribute = $value;
				$m->save();
				
				\app\components\Log::save($id, $action);
			}
		}	
			
        return json_encode($response);
    }
} 
?>