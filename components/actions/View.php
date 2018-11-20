<?php 
namespace app\components\actions;

use Yii;
use yii\web\NotFoundHttpException;

class View extends \yii\base\Action
{
    public $model;
    
    public function run()
    {
        $model = $this->model;
		
        $id = (int) Yii::$app->request->getQueryParam('id');
		$model = $model::find()->where(['id'=>$id])->one();
        
        if ($model == null) 
            throw new NotFoundHttpException('Страница не найдена.');
        
     		
		return $this->controller->render('view', [
			'model' => $model,
		]);
  
    }
} 
?>