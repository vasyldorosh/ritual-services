<?php 
namespace app\components\actions;

use Yii;
use yii\web\NotFoundHttpException;

class Update extends \yii\base\Action
{
    public $model;
    public $params;
    public $multilingual=false;
    public $redirectConfig=false;
    
    public function run()
    {
		$model = $this->model;
		
        $id = (int) Yii::$app->request->getQueryParam('id');
		
		if ($this->multilingual)
			$model = $model::find()->where(['id'=>$id])->multilingual()->one();
		else
			$model = $model::find()->where(['id'=>$id])->one();
        	
        if ($model == null) 
            throw new NotFoundHttpException('Страница не найдена.');
        
		//d($this->params);
		
		if (!empty($this->params)) {
			foreach ($this->params as $k=>$v)
			$model->{$k} = $v;
		}
		
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Изменения сохранены.');
			
			\app\components\Log::save($model->id);
			
            return $this->controller->afterSaveRedirect($model, $this->redirectConfig);
        } else {
			
            return $this->controller->render('update', [
                'model' => $model,
            ]);
        }
    }
} 
?>