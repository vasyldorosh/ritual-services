<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use app\modules\admin\models\LoginForm;
use app\modules\admin\models\RemindForm;
use app\components\AccessControl;
use app\components\Event;
use app\modules\admin\models\Admin;


class AuthController extends \app\components\AdminController
{
    
    public function behaviors()
    {
        return [
			'access' => [
               'class' => AccessControl::className(),
               'rules' => [
                   [
                       'actions' => ['login', 'remind', 'remind-send-password'],
                       'allow' => true,
                       'matchCallback' => function() {
                           return admin()->isGuest;                    
                       }
                   ],
                   [
                       'actions' => ['logout', 'index'],
                       'allow' => true,
                       'matchCallback' => function() {
                           return !admin()->isGuest;                    
                       }
                   ],
               ],
			],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction'
            ],
        ];
    }
    
    public function actionLogin()
    {
        $this->layout = '@app/views/layouts/admin/main-login';
       
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {
			$admin = $model->getUser();
			if ($model->login()) {
				\app\components\Log::save($admin->id, 'admin.login.success', ['admin_id'=>$admin->id]);
				return $this->redirect(['/admin/index/index']);
			} else {
				if ($admin) {
					\app\components\Log::save($admin->id, 'admin.login.failed', ['admin_id'=>$admin->id]);
				}				
			}
        }
		return $this->render('login', [
			'model' => $model
        ]);
    }
    
    public function actionRemind()
    {
		$this->layout = '@app/views/layouts/admin/main-login';
        
		if (\Yii::$app->session->hasFlash('success_remind')) {
			return $this->render('remind_message', [
                'message' => \Yii::$app->session->getFlash('success_remind'),
            ]);			
		}
		
        $model = new RemindForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			
			$admin = Admin::find()->where(['email'=>$model->email])->one();
			
			Yii::$app->event->send(1, [
				'username'	=> $admin->name,
				'email' 	=> $admin->email,
				'link' 		=> Yii::$app->urlManager->createAbsoluteUrl(['admin/auth/remind-send-password', 'code'=>base64_encode(serialize(['email'=>$admin->email, 'password'=>$admin->password_hash]))]),
			]);
					
			Yii::$app->session->setFlash('success_remind', 'Инструкция для смены пароля отправлена на E-mail: <b>'.$model->email.'</b>');
			
			$this->refresh();
			
        } else {
            return $this->render('remind', [
                'model' => $model
            ]);
        }
    }
    
    public function actionRemindSendPassword($code)
    {
		$this->layout = '@app/views/layouts/admin/main-login';
        $data = unserialize(base64_decode($code));
	
		$admin = Admin::find()->where(['email'=>$data['email'], 'password_hash'=>$data['password']])->one();
		
		if (!empty($admin)) {
			
			$password = Yii::$app->getSecurity()->generateRandomString();
			$admin->password_hash = $password;
			$admin->save();
			
			Yii::$app->event->send(2, [
				'username'	=> $admin->name,
				'email' 	=> $admin->email,
				'password' 	=> $password,
			]);			
			
			return $this->render('remind_message', [
                'message' => 'Новый пароль отправлен на почту: <b>'.$data['email'].'</b>',
            ]);				
			
		} else {
			return $this->render('remind_message', [
                'message' => 'Страница не найдена.',
            ]);				 
		}
    }
    
    public function actionLogout()
    {
        \app\components\Log::save(admin()->id, 'admin.logout', ['admin_id'=>admin()->id, 'skip'=>true]);
		admin()->logout(false);
		session()->set("root_id", 0);

        return $this->redirect(['login']);
    }
    
    public function actionIndex()
    {
        return $this->render('index');
    }
}