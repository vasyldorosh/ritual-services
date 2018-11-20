<?php
namespace app\modules\settings\models;

use Yii;

class Settings
{
    protected $_data = null;
    protected static $_instance = null;

    private function __construct() {}
    private function __clone() {}

    /**
     * @static
     * @return Settings
     */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Получить массив всех данных конфига
     * @return array
     */
    public function getData() {
        $this->_init();
        return $this->_data;
    }

    /**
     * Вернуть значение параметра конфига по его имени
     * 
     * @param string $name
     * @param bool $defaultValue
     * @return mixed
     */
    public function getValue($name, $defaultValue='') {
        $this->_init();
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        }
        return $defaultValue;
    }

	private function _init() {
        if (is_null($this->_data)) {
			
			$this->_data = SettingsModel::getAll();		
		}
    }
	
}