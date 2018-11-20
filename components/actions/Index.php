<?php 
namespace app\components\actions;

use Yii;

class Index extends \yii\base\Action
{
    public $view = 'index';
    public $search;
    
    public function run()
    {
        $searchModel = new $this->search;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->controller->render($this->view, [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
} 
?>