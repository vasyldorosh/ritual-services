<?php 
namespace app\components\actions;

use Yii;

class Create extends \yii\base\Action
{
    public $model;
    public $scenario;
    public $attributes = [];
	public $redirectConfig=false;
    
    public function run()
    {
		$model = empty($this->scenario) ? new $this->model : new $this->model(['scenario' => $this->scenario]);
		
		$model->attributes = $this->attributes;
		
		if (r()->isPost) {
			$attributes = r()->post($model->formName());
			$attributes = array_merge($attributes, $this->attributes);
			$model->attributes = $attributes;
			
			if ($model->load(Yii::$app->request->post()) && $model->save()) {
				Yii::$app->session->setFlash('success', 'Успешно создано');
				
				\app\components\Log::save($model->id);
				
				return $this->controller->afterSaveRedirect($model, $this->redirectConfig);
			}
		}

        return $this->controller->render('create', [
			'model' => $model,
        ]);
    }
} 
?>