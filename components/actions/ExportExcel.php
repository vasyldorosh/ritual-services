<?php 
namespace app\components\actions;

use Yii;
use yii\web\NotFoundHttpException;

class ExportExcel extends \yii\base\Action
{
    public $model;
    
    public function run()
    {
        $model = new $this->model;

		$model->exportToExcel();
    }
} 
?>