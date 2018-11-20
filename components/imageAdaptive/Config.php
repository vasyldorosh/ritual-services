<?php

namespace app\components\imageAdaptive;

use Yii;

class Config {
	
    protected $_data = null;
    protected static $_instance = null;

    private function __construct() {}
    private function __clone() {}

    /**
     * @static
     * @return ImageAttributeConfig
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
    public function getValue($name, $defaultValue = true) {
        $this->_init();
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        }
        return $defaultValue;
    }

    private function _init() {
        if (is_null($this->_data)) {
            if ($this->_loadFromFile()) {
                return true;
            }
            
        }
        //throw new CException('Ошибка при загрузке конфига приложения');
    }

    private function _loadFromFile() {
        $filePath = $this->_getFilePath();
		if (file_exists($filePath)) {  	
			$this->_data = require($filePath);
            return true;
        }
        return false;
    }

    private function _getFilePath() {
        return Yii::getAlias('@app') . '/config/image.php';
    }

}
