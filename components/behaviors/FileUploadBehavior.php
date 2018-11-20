<?php
namespace app\components\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\UnknownPropertyException;
use yii\base\InvalidConfigException;
use yii\helpers\Inflector;
use yii\validators\Validator;
use yii\web\UploadedFile;
use yii\db\ActiveRecord;

class FileUploadBehavior extends Behavior
{
    /*
     * Атрибут модели для хранения изображения
     */
    public $attributeName = ['file'];

    /*
     * Загружаемое изображение
     */
    public $file;
    
	/*
     * Минимальный размер загружаемого файла
     */
    public $minSize = 0;

    /*
     * Максимальный размер загружаемого файла
     */
    public $maxSize = 53687091200;

    /*
     * Допустимые типы изображений
     */
    public $extensions = '';
    /*
     * Список сценариев в которых будет использовано поведение
     */
    public $scenarios = array('insert', 'update');
    /*
     * Директория для загрузки изображений
     */
    public static $uploadPath="/upload/";
    /*
     * Список сценариев в которых изображение обязательно, 'insert, update'
     */
    public $requiredOn;

    protected $_newFile=[];
    protected $_oldFile=[];

    /**
     * @events
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            ActiveRecord::EVENT_BEFORE_INSERT  => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE  => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_DELETE  => 'beforeDelete',
        ];
    }

    /**
     * @attach
     */
    public function attach($owner)
    {
        parent::attach($owner);

        if ($this->checkScenario())
        {
            foreach($this->attributeName as $attrName){
                $owner->validators[] = Validator::createValidator('file', $owner, $attrName, array(
                    //'extensions' => $this->extensions,
                    //'minSize'    => $this->minSize,
                    //'maxSize'    => $this->maxSize,
                    //'skipOnEmpty'=> false,
                ));
            }
        }		
    }
	
    public function afterFind($event)
    {
		$request = Yii::$app->request;
		
		if (method_exists($request, 'get')) {
			
			$r = $request->get('r');
			
			if (!empty($r))
				foreach($this->attributeName as $attrName){
					if (isset($this->owner{$attrName}))
						$this->_oldFile[$attrName] = $this->owner{$attrName};
				}
		}
    }

    public function beforeValidate($event)
    {
	   foreach($this->attributeName as $attrName){
            if ($this->checkScenario() && ($this->_newFile[$attrName] = UploadedFile::getInstance($this->owner, $attrName))) {
                $this->owner->{$attrName} = $this->_newFile[$attrName];
			}
        }
    }

    public function beforeSave($event)
    {
        foreach($this->attributeName as $attrName){
			if (isset($this->owner->{"is_delete_".$attrName}) && $this->owner->{"is_delete_".$attrName}) {
				$this->deleteFile($attrName);
				if (!isset($this->_newFile[$attrName])) {
					$this->owner->$attrName = '';
				}
			}
			
            if ($this->checkScenario() && isset($this->_newFile[$attrName]) && $this->_newFile[$attrName] instanceof UploadedFile)
            {
                $this->saveFile($attrName);
                $this->deleteFile($attrName);
            }
        }

    }

    public function beforeDelete($event)
    {
        foreach($this->attributeName as $attrName){
            $this->deleteFile($attrName);
        }

    }

    public function deleteFile($attrName)
    {
		if (isset($this->_oldFile[$attrName])) {
			$file = Yii::getAlias('@webroot') . self::$uploadPath . $this->_oldFile[$attrName];
			
			if (@is_file($file)) {
				$pi = pathinfo($file);
				
				@unlink($file);
				
				// если нет файлом удаляем папку
				if (!count(glob($pi['dirname'] . '/*'))) {
					@rmdir($pi['dirname']);
				}
				// если нет файлом удаляем папку
				if (!count(glob($pi['dirname'] . '/../*'))) {
					@rmdir($pi['dirname'] . '/../');
				}				
			}		
		}		
    }

    /*
     * Проверяет допустимо ли использовать поведение в текущем сценарии
     */
    public function checkScenario()
    {
        #return in_array($this->owner->scenario, $this->scenarios);
        return true;
    }

    public function saveFile($attrName)
    {
        $tmpName = $this->_newFile[$attrName]->tempName;
			
        $fileName = $this->_newFile[$attrName]->name;
        $randPath = strtolower(Yii::$app->getSecurity()->generateRandomString());
       
        $rand = substr($randPath,0,2)."/".substr($randPath,2,2)."/";
		$path = Yii::getAlias('@webroot') . self::$uploadPath . $rand;
		
        if ( ! $newFile = self::pathIsWritable($path, $fileName))
            die('Директория "'.$path.'" не доступна для записи!');

        file_put_contents($newFile, file_get_contents($tmpName));
	   
		$this->owner->{$attrName} = $rand . pathinfo($newFile, PATHINFO_BASENAME);
    }

    public static function pathIsWritable($path, $name)
    {
        if (self::checkPath($path))
            return $path . $name;
        else
            return false;
    }

    public static function checkPath($path)
    {
		//d($path);
		
        if (!is_dir($path)){// проверка на существование директории
            if (!is_dir(substr($path,0,-3))){
                mkdir(substr($path,0,-3));
            }
            return mkdir($path);// возвращаем результат создания директории
        }

        else if (!is_writable($path)) // проверка директории на доступность записи
            return false;
        
		return true; // папка существует и доступна для записи
    }

    /**
     * @param $attr_name
     * @return string
     */
    public function getFileUrl($attr_name)
    {
		if (!empty($this->owner->$attr_name)) 
			return  self::$uploadPath . $this->owner->$attr_name;
		else 
			return '';
    }	

	
}
