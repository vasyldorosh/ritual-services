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

class ImageUploadUrlBehavior extends Behavior
{
    // Master Dimension
    const NONE = 1;
    const AUTO = 2;
    const HEIGHT = 3;
    const WIDTH = 4;
    // Flip Directions
    const HORIZONTAL = 5;
    const VERTICAL = 6;
    /*
     * Атрибут модели для хранения изображения
     */
    public $attributeName = ['image'];

    /*
     * Загружаемое изображение
     */
    public $image;
    /*
     * Минимальный размер загружаемого изображения
     */
    public $minSize = 0;

    /*
     * Максимальный размер загружаемого изображения
     */
    public $maxSize = 53687091200;

    /*
     * Допустимые типы изображений
     */
    public $extensions = 'jpg,jpeg,png,gif';
    /*
     * Список сценариев в которых будет использовано поведение
     */
    public $scenarios = array('insert', 'update');
    /*
     * Директория для загрузки изображений
     */
    public $uploadPath="/upload/";
    /*
     * Список сценариев в которых изображение обязательно, 'insert, update'
     */
    public $requiredOn;

    /*
     * Callback функция для генерации имени загружаемого файла
     */
    public $imageNameCallback;
    /*
     * Параметры для ресайза изображения
     */
    public $resize = ['quality' => 100];

    protected $_newImage=[];
    protected $_oldImage=[];

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
    }
	
    public function afterFind($event)
    {
			foreach($this->attributeName as $attrName){
				if (isset($this->owner{$attrName}))
					$this->_oldImage[$attrName] = $this->owner{$attrName};
			}

    }

    public function beforeValidate($event)
    {
	   /*
	   foreach($this->attributeName as $attrName){
			
            if (!empty($this->owner->$attrName) && $this->owner->$attrName != $this->_oldImage[$attrName]) {
              
				$expl 	= explode("base64,", $this->owner->$attrName);
				$ext 	= str_replace(';', '', explode('/', explode(':', $expl[0])[1])[1]);
				$base64 = $expl[1];
				
				$error = false;
				if (!$base64 == base64_encode(base64_decode($base64))) {
					$error = 'File not valid basa64';
				} else {
					$is_image = @imagecreatefromstring(base64_decode($base64));
					if ($is_image===false) {
						$error = 'File not a valid image';
					}					
				}
				
				if ($error !== false) {
					$this->owner->addError($attrName, $error);
				} else {
					$this->owner->{$attrName} = [
						'ext' 		=> $ext,
						'content' 	=> base64_decode($base64),
					];
				}
				
				//d($this->owner->$attrName);
			}
        }
		*/
    }

    public function beforeSave($event)
    {
        foreach($this->attributeName as $attrName){
		    if (!empty($this->owner->{$attrName}) && (empty($this->_oldImage[$attrName]) || (isset($this->_oldImage[$attrName]) && $this->owner->$attrName != $this->_oldImage[$attrName]))) {
                $this->saveImage($attrName);
                $this->deleteImage($attrName);
            }
        }
    }

    public function beforeDelete($event)
    {
        foreach($this->attributeName as $attrName){
            $this->deleteImage($attrName);
        }

    }

    public function deleteImage($attrName)
    {
		if (isset($this->_oldImage[$attrName])) {
			$file = Yii::getAlias('@webroot') . $this->uploadPath . $this->_oldImage[$attrName];
			
			if (@is_file($file)) {
				// Удаляем связанные с данным изображением превьюшки:
				$pi = pathinfo($file);
				$dirName = $pi['dirname'] . '/';
				$fileName = $pi['basename'];
				$fileArray = glob($dirName . 'c_*_' . $fileName) + glob($dirName . 'r_*_' . $fileName);
				if(is_array($fileArray)){
					foreach ($fileArray as $f) {
						@unlink($f);
					}
				}
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

    public function saveImage($attrName)
    {
		$imageUrl 	= $this->owner->$attrName;
        $randPath 	= strtolower(Yii::$app->getSecurity()->generateRandomString());
		$expl 		= explode('.', $imageUrl);
		$ext 		= end($expl);
        $imageName 	= $randPath . '.' . $ext;
	   
        $rand = substr($randPath,0,2)."/".substr($randPath,2,2)."/";
		$path = Yii::getAlias('@webroot') . $this->uploadPath . $rand;
		
        if ( ! $newFile = self::pathIsWritable($path, $imageName))
            die('Директория "'.$path.'" не доступна для записи!');
		
		$status = \app\helpers\Http::getStatusByUrl($imageUrl);
		
		if ($status == 200) {
			file_put_contents($newFile, file_get_contents($imageUrl));
			$this->owner->{$attrName} = $rand . pathinfo($newFile, PATHINFO_BASENAME);
			return true;
		} else {
			return false;
		}
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
     * @param $attr_name - имя атрибута изображения
     * @param int $width - ширина изображения
     * @param int $height - высота изображения
     * @param $dimension - соотношение сторон
     * @return string
     */
    public function makeThumbnail($attr_name, $width = 0, $height = 0, $mode='resize', $dimension)
    {
		if ($dimension=='')
			$dimension = self::AUTO;
	
        $quality = isset($this->resize['quality']) ? $this->resize['quality'] : 100;
        $width = $width === 0
            ? $height
            : $width;

        $height = $height === 0
            ? $width
            : $height;
        $image=$this->owner->$attr_name;
        $basePath=Yii::getAlias('@webroot'). $this->uploadPath;
        if (!file_exists( $basePath. $image) || $image==null)
            return null;

        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $pos = strrpos($image, "/");
		$fileMode = $mode=='resize'?'r':'c';
		$file = substr($image,0,$pos).'/'. $fileMode . '_' . $width . 'x' . $height . '_' . pathinfo($image, PATHINFO_BASENAME);

        if (!file_exists($basePath . $file) && file_exists(Yii::getAlias('@webroot') . $file) === false) {
            $thumb = Yii::$app->image->load($basePath . $image)->quality($quality);
			if ($mode=='resize')
				$thumb->resize($width, $height,$dimension);
			else 
				$thumb->crop($width, $height,$dimension);
            $thumb->save($basePath . $file,FALSE);
        }

        return $this->uploadPath . $file;
    }

    /**
     * @param $attr_name
     * @param int $width
     * @param int $height
     * @param int $dimension
     * @return string
     */
    public function getImageUrl($attr_name, $width = 0, $height = 0, $mode='resize', $dimension=self::AUTO)
    {
        return  (
        ($width > 0 || $height > 0) && (
        $thumbnail = $this->makeThumbnail($attr_name,$width, $height, $mode, $dimension)
        ) !== null
            ? $thumbnail
            : $this->uploadPath . $this->owner->{$attr_name}
        );
    }	

}
