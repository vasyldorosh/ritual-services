<?php

namespace app\components;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use app\components\AccessControl;
use app\widgets\LoginForm;

class AdminController extends Controller
{
	public $layout = '@app/views/layouts/admin/main';
    
	public $side = 'module';
		
	public function getAccessActionMap()
	{
		return [
			'multipleDeactivate'	=> 'deactivate',
			'multipleActivate' 		=> 'activate',
			'multipleDelete' 		=> 'delete',
			'multipleColumn' 		=> 'update',
		];
	}
	
	public static function getExceptActions()
	{
		return [
		];
	}
			
    public function behaviors()
    {
        return [
			'access' => [
               'class' => AccessControl::className(),
               'except' => static::getExceptActions(),
               'rules' => [
                   [
                       'actions' => [$this->action->id],
                       'allow' => true,
                       'matchCallback' => function() {
						    if (admin()->isGuest) {
								return false;
							}
						   
							$actionId	= Yii::$app->controller->action->id;
							$action 	= isset($this->getAccessActionMap()[$actionId]) ? $this->getAccessActionMap()[$actionId] : $actionId;
						   
						    $access = AccessControl::can(Yii::$app->controller->module->id . '.' . Yii::$app->controller->id . '.' . $action);
						   
							if (!$access && Yii::$app->request->isAjax) {		
								Yii::$app->response->statusCode = 403;
								Yii::$app->response->statusText = 'Access forbidden!';
								Yii::$app->response->send();
								exit();
							} else if (!$access){
								return $this->redirect(['/admin/admin/access-denied']);
							}
							
							return $access;
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
                'class' => 'yii\web\ErrorAction',
				'view' => '@app/views/error.php',
            ],
        ];
    }	
	
	/**
     * Редирект после сохранения модели в зависимости от входных параметров (на эту же страницу,
     * на пред страницу или просто на список моделей)
     */
    public function afterSaveRedirect($model = null, $redirectConfig=null) 
    {
		$apply = Yii::$app->request->post('apply');
        
		//d($apply);
			
		$redirectData = ['index'];
			
		if (!empty($model) && $apply === null && !empty($redirectConfig)) {
        	$r = '';
			
			if (is_array($redirectConfig)) {
				$r = '/?r=' . $redirectConfig['path'];
				$gets = [];
				foreach ($redirectConfig['dattr'] as $q_param=>$attribute) {
					$gets[] = sprintf('%s=%s', $q_param, $model->$attribute);
				}
				foreach ($redirectConfig['sattr'] as $q_param=>$q_val) {
					$gets[] = sprintf('%s=%s', $q_param, $q_val);
				}
				
				$r .= '&' . implode('&', $gets);
				
			} else {
				$r = $redirectConfig;
			}
			
			
			$redirectData = $r;
			
		} else if ($apply === null && Yii::$app->request->get('from', false)) {
        	
            $redirectData = base64_decode(Yii::$app->request->get('from'));
		
		} else if ($apply !== null && !is_null($model)) {
        	
            $redirectData = ['update', 'id' => $model->id];

            if (Yii::$app->request->get('from', false)) {
                $redirectData += ['from' => Yii::$app->request->get('from')];
            }            
        } 
        
		return $this->redirect($redirectData);
    }	
	
	public function beforeAction($action) {
		if (r()->isAjax) {
			$lang = r()->get('lang');
			if (!empty($lang)) {
				Yii::$app->language = $lang;
			}
		}
		
		return parent::beforeAction($action);
	}	
	
}