<?php 

namespace app\components;

use Yii;
use Closure;
use yii\helpers\Html;
use yii\helpers\Url;

class ActionColumn extends \yii\grid\ActionColumn
{
	public $access = null;
	
	public $from = null;
	
	public $module 		= null;
	public $controller 	= null;
	
    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons()
    {
		$module 	= !empty($this->module) ? $this->module : Yii::$app->controller->module->id;
		$controller = !empty($this->controller) ? $this->controller : Yii::$app->controller->id;
		
		
		if (empty($this->access))
			$this->access = $module . '.' . $controller . '.';
		
		$rule = $this->access; 
		
		
		$from = base64_encode($this->from);
		
		if (empty($from)) {		
			$from = r()->get('from');
			$from = !empty($from) ? $from : base64_encode(r()->url);
		}
		
		if (!isset($this->buttons['view']) && \app\components\AccessControl::can($rule.'view')) {
            $this->buttons['view'] = function ($url, $model, $key) use ($from, $module, $controller){
                $options = array_merge([
                    'title' => Yii::t('yii', 'View'),
                    'aria-label' => Yii::t('yii', 'View'),
                    'data-pjax' => '0',
					'class' => 'js-redirect',
                ], $this->buttonOptions);
				
				$link = sprintf("/?r=%s/%s/%s&id=%d", $module, $controller, 'view', $model->id);
	            return Html::a('<i class="icon-eye-open"></i>', $link.'&from='.$from, $options);
            };
        }
        if (!isset($this->buttons['update']) && \app\components\AccessControl::can($rule.'update')) {
            $this->buttons['update'] = function ($url, $model, $key) use ($from, $module, $controller){
                $options = array_merge([
                    'title' => Yii::t('yii', 'Update'),
                    'aria-label' => Yii::t('yii', 'Update'),
                    'data-pjax' => '0',
					'class' => 'js-redirect',
                ], $this->buttonOptions);
				
				$link = sprintf("/?r=%s/%s/%s&id=%d", $module, $controller, 'update', $model->id);
	            return Html::a('<i class="icon-edit"></i>', $link.'&from='.$from, $options);
            };
        }
        if (!isset($this->buttons['delete']) && \app\components\AccessControl::can($rule.'delete')) {
            $this->buttons['delete'] = function ($url, $model, $key) use ($from, $module, $controller){
                $options = array_merge([
                    'title' => Yii::t('yii', 'Delete'),
                    'aria-label' => Yii::t('yii', 'Delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'data-method' => 'post',
                    'data-pjax' => '1',
                ], $this->buttonOptions);
				
				$link = sprintf("/?r=%s/%s/%s&id=%d", $module, $controller, 'delete', $model->id);
	            return Html::a('<i class="icon-trash"></i>', $link.'&from='.$from, $options);
            };
        }
        if (!isset($this->buttons['log']) && \app\components\AccessControl::can('admin.log.index')) {
            $this->buttons['log'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => 'Создатель',
                    'aria-label' => 'Создатель',
                    'data-pjax' => '0',
                    'target' => '_blank',
                    'class' => 'js-redirect',
                ], $this->buttonOptions);
				
                return Html::a('Лог', ['/admin/log/index', 'Log[action]'=>$this->access.'create', 'Log[model_id]'=>$model->id], $options);
            };
        }
    }
	
    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        return '<div class="grid-action-icons">' . preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) use ($model, $key, $index) {
            $name = $matches[1];
            if (isset($this->buttons[$name])) {
                $url = $this->createUrl($name, $model, $key, $index);

                return call_user_func($this->buttons[$name], $url, $model, $key);
            } else {
                return '';
            }
        }, $this->template) . '</div>';
    }	
		
}