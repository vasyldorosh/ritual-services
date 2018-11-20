<?php 
namespace app\components\actions;

use Yii;
use yii\web\NotFoundHttpException;

class Delete extends \yii\base\Action
{
    public $model;
	public $redirectConfig=false;
    
    public function run()
    {
        $model = $this->model;
		$id = (int) Yii::$app->request->getQueryParam('id');
		
		$model = $model::find()->where(['id'=>$id])->one();
        
        if ($model == null) 
            throw new NotFoundHttpException('Страница не найдена.');
        
		$model->delete();
		\app\components\Log::save($model->id);
		
        return $this->controller->afterSaveRedirect($model, $this->redirectConfig);
    }
} 
?>