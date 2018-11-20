<?php 
namespace app\components;

use Yii;

class GridView extends \yii\grid\GridView
{
	 public $tableOptions = ['class' => 'items table table-striped table-bordered'];
	
	public $module 		= null;
	public $controller 	= null;
	public $actions 	= [];
	public $exports 	= [];
	
    public function renderSection($name)
    {
        switch ($name) {
            case '{actions}':
                return $this->renderActions();
            case '{exports}':
                return $this->renderExports();
            default:
                return parent::renderSection($name);
        }
    }	
	
    /**
     * Renders the sorter.
     * @return string the rendering result
     */
    public function renderActions()
    {
		$html = '';
		
		foreach ($this->actions as $k=>$data) {
			$action 	= isset($data['action']) ? $data['action'] : $k;
			
			$controller = !empty($this->controller) ? $this->controller : Yii::$app->controller->id;
			$module 	= !empty($this->module) ? $this->module : Yii::$app->controller->module->id;
			
			$rule =  isset($data['access']) ? $data['access'] : $module . '.' . $controller . '.' . $action;
			
			if (AccessControl::can($rule))
				$html .= ' <button data-grid-id="'.$this->id.'" data-url="'.$data['url'].'" type="button" class="disabled btn btn-xs '.$data['class'].'">'.$data['label'].'</button>';
		}

		return '<div class="grid-actions">' . $html . '</div>';
    }	
	
   /**
     * Renders the sorter.
     * @return string the rendering result
     */
    public function renderExports()
    {
		$html = '';
		
		foreach ($this->exports as $k=>$data) {
			$action 	= isset($data['action']) ? $data['action'] : $k;
			
			$controller = !empty($this->controller) ? $this->controller : Yii::$app->controller->id;
			$module 	= !empty($this->module) ? $this->module : Yii::$app->controller->module->id;
			
			$rule = $module . '.' . $controller . '.' . $action;
			
			if (AccessControl::can($rule))
				$html .= ' <a data-grid-id="'.$this->id.'" data-pjax="0" href="'.$data['url'].'" class="btn btn-xs '.$data['class'].'">'.$data['label'].'</a>';
		}

		return '<div class="grid-exports" style="float: right;margin-top: -40px;">' . $html . '</div>';
    }	
	
}