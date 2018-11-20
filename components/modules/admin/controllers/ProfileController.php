<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use app\components\AccessControl;
use app\modules\admin\models\Admin;
use yii\web\NotFoundHttpException;

class ProfileController extends \app\components\AdminController {

    public $side = 'module';

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' => ['index'],
                'rules' => [
                    [
                        'actions' => [$this->action->id],
                        'allow' => true,
                        'matchCallback' => function() {
                            !admin()->isGuest;
                        }
                    ],
                ],
            ],
        ];
    }

    public function actionIndex() {
		$model = Admin::findOne((int)admin()->id);
		
		if (empty($model)) {
			 throw new NotFoundHttpException('Страница не найдена.');
		}
		
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Изменения сохранены.');
			
			\app\components\Log::save($model->id);
			
            return $this->redirect(['/admin/index/index']);
        } else {
				
			return $this->render('index', [
				'model'=>$model,
			]);
        }		
    }

}
