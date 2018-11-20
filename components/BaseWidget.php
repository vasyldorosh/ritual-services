<?php

namespace app\components;

use Yii;
use yii\base\Widget;
use yii\web\NotFoundHttpException;

class BaseWidget extends Widget 
{	
	public $errors 	   = [];
	
	/**
     * Показывать в структуре
     * @var bool
     */
    public static $showInStructure = true;	
	
 	/**
     * Действие
     * @var string
     */
    public $action;	
	

	public function setAttributes($values) {
		
		if (is_array($values)) {
			foreach ($values as $k=>$v) {
				$this->$k = $v;
			}
		}
	}
	
    /**
     * return string
     */
    public function getName()
	{
		return '';
	}

    /**
     * return array
     */
    public function rules()
	{
		return [
			[['action'], 'required'],
		];
	}
	
    /**
     * return array
     */
    public function attributes()
	{
		return [
			'action' => 'Действие',
		];
	}
	
    /**
     * return sttring
     */
    public function getAttributeLabel($attribute)
	{
		$list = $this->attributes();
		
		if (isset($list[$attribute])) {
			return $list[$attribute];
		} else {
			return false;
		}
	}
	
	public function compact() {
		$data = [];
		foreach ($this->attributes() as $property=>$value) {
			$data[$property] = $this->$property;
		}
		return $data;
	}
	
    public function validate()
	{
		$model = \yii\base\DynamicModel::validateData($this->compact(), $this->rules());
		
		if ($model->hasErrors()) {
			$this->errors = $this->normalizeErrors($model->errors);
			
			return false;
		} else {
			return true;
		}	
	}
	
	public function normalizeErrors($errors)
	{
		$data = [];
		
		foreach ($errors as $attribute=>$errs) {
			$label = $this->getAttributeLabel($attribute);
				
			foreach ($errs as $k=>$v) {
				$data[$attribute][$k] = preg_replace('#«.*»#sUi', sprintf("«%s»", $label), $v);
			}	 
		}
		
		return $data;
	}
	
    /**
     * return array
     */
    public static function getActions()
	{
		return [];
	}
	
    /**
     * return array
     */
    public static function onlySuperAdminActions()
	{
		return [];
	}
	
    /**
     * return array
     */
    public static function getActionsSelect()
	{
		if (is_super_admin()) {
			return static::getActions();
		} else {
			$onlySuper = static::onlySuperAdminActions();
			if ($onlySuper === true) {
				return [];
			} 
			
			$list = static::getActions();
			foreach ($list as $k=>$v) {
				if (in_array($k, static::onlySuperAdminActions())) {
					unset($list[$k]);
				}
			}
			
			return $list;
		}	
	}
	
	
	
   /**
     * return array
     */
    public static function getActionTitle($action)
	{
		$list = static::getActions();
		if (isset($list[$action])) {
			return $list[$action];
		}
	
	}
	
	public function innerRun()
	{
		return true;
	}
	
    /**
     * Роутер
     */	
	public function run()
    {
        if (Yii::$app->controller->id == 'page') {
			echo static::getName() . '. ' . self::getActionTitle($this->action);
			return;
		}
		
		$this->innerRun();
		
		//получаем все допустимые действия
        $actionsList = static::getActions();
		
		if (is_array($actionsList) && count($actionsList) && isset($actionsList[$this->action]) && method_exists($this, 'action' . ucfirst($this->action))) {
		    $this->{sprintf('action%s', $this->action)}();
        }       
    }

	public function getVariables() 
	{
		return Yii::$app->controller->getVariables();
	}
	
	public function page404() 
	{
		throw new NotFoundHttpException( t('Страница не найдена.') );
	}
	
	public function redirect($url) 
	{
		header('location: ' . $url);
	}
	
	public function end_json($data) 
	{	
		echo json_encode($data);
		exit();
	}
	
	public function end_html($data) 
	{	
		echo $data;
		exit();
	}
	
	public function getMode()
	{
		return r()->get('mode');
	}
}