<?php

namespace app\components\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\StringHelper;
use app\components\imageAdaptive\Config;
use app\components\imageAdaptive\Manager;


class ImageAdaptiveUploadBehavior extends Behavior
{
	public $post 		= [];
	public $oldImages	= [];
	public $newImages	= [];

	public $uploadErrors = [];
	
    /*
     * Атрибут модели для хранения изображения
     */
    public $attributeName = [];

    /*
     * Допустимые типы изображений
     */
    public $types = 'jpg,jpeg,png,gif';

    /*
     * Директория для загрузки изображений
     */
    const UPLOAD_PATH="/upload/";
	
	public $resize = array('quality' => 100);
	
    /**
     * @events
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND  	=> 'afterFind',
			ActiveRecord::EVENT_BEFORE_VALIDATE => 'afterValidate',
            ActiveRecord::EVENT_BEFORE_INSERT  	=> 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE  	=> 'beforeSave',
            ActiveRecord::EVENT_BEFORE_DELETE  	=> 'beforeDelete',
        ];
    }	
	
    public function afterValidate($event)
    {
		if (empty(Yii::$app->request)) {
			return;
		}
		
		
		$ownerName = StringHelper::basename(get_class($this->owner));
	
		$config = Config::getInstance()->getData();
		$this->post = Yii::$app->request->post($ownerName);
		
		foreach($this->attributeName as $attrName){
			if (isset($config[$ownerName]) && isset($this->post[$attrName])) {
				
				$attributeConfig = $config[$ownerName][$attrName];
				
				foreach ($attributeConfig['sizes'] as $size=>$sizeConf) {
				
					$attributeConfig = $config[$ownerName][$attrName];
					
					$image64 = $this->post[$attrName][$attrName.'_'.$size];
					$this->validateImage($attrName.'_'.$size, $attributeConfig['label'], $sizeConf['width'], $sizeConf['height'], $image64);					
	
					if ($attributeConfig['i18n']) {
						 foreach (array_keys(Yii::$app->params['otherLanguages']) as $lang) {
							$image64 = $this->post[$attrName][$attrName.'_'.$size.'_'.$lang];
							$this->validateImage($attrName.'_'.$size.'_'.$lang, $attributeConfig['label'] .' '.$lang, $sizeConf['width'], $sizeConf['height'], $image64);
						 }						
					}
				}
			}
        }	
    }
	
	private function validateImage($attribute, $label, $width, $height, $image)
	{
		if ($this->isBase64($image) && false) {
			$size = $this->getImageSize($image);
			if ($width!=$size['width'] || $height!=$size['height']) {
				$msgError = $label.' '. $width.'x'.$height.': размер должен бить - '.$width.'x'.$height;
				$this->uploadErrors[$attribute] = $msgError;
				$this->owner->addError($attribute, $msgError);
			} 			
		}
	}
		
	private function isBase64($str)
	{
		return stripos($str, ';base64');
	}
	
	private function setImageData($attribute, $attr, $size, $image64, $lang='')
	{
		if (!empty($image64)) {
			$expl = explode(';', $image64);	
			$mime = explode(':', $expl[0]);
			$mime = end($mime);
			$expl = explode('/', $mime);
			$ext = end($expl);
			$filename =  $size . '_' . $this->post[$attribute][$attr.'_filename'];
			$rand = strtolower(Yii::$app->getSecurity()->generateRandomString());
			
			$keyAttribute = ($lang=='') ? $attribute : $attribute .'_'.$lang;
			$path = substr($rand, 0, 2) . '/' . substr($rand, 2, 2) . '/' . $filename;
			
			$attrs = [
				'path' => $path,
				'align' => isset($this->post[$attribute][$attr.'_align'])?$this->post[$attribute][$attr.'_align']:'',
				'background_size' => isset($this->post[$attribute][$attr.'_background_size'])?$this->post[$attribute][$attr.'_background_size']:'',
			];
			
			if (isset($this->post[$attribute][$attr.'_position'])) {
				$attrs['position'] = $this->post[$attribute][$attr.'_position'];
			}
			
			$this->newImages[$keyAttribute][$size] = $attrs;

			//saveimage
			$dir = Yii::getAlias('@webroot') . self::UPLOAD_PATH . substr($rand,0,2) . '/';
			if (!is_dir($dir)) {
				mkdir($dir);
			}
			$dir .= substr($rand,2,2) . '/';
			if (!is_dir($dir)) {
				mkdir($dir);
			}

			$content = str_replace('data:image/'.$ext.';base64,', '', $image64);
			$content = str_replace(' ', '+', $content);
			$content = base64_decode($content);

			file_put_contents($dir . $filename, $content);			
		}
	}

	public function afterFind($event)
	{
		$request = Yii::$app->request;
		
		if (!method_exists($request, 'get')) {
			return;
		}
		
		$ownerName = StringHelper::basename(get_class($this->owner));
		
		$r = Yii::$app->request->get('r');
		if (!empty($r)) {
			foreach (Manager::getAttributes($ownerName, true) as $attribute) {
				$this->oldImages[$attribute] = !empty($this->owner->{$attribute})?json_decode($this->owner->{$attribute}, 1):[];
			}
		}
		
		foreach ($this->oldImages as $attr=>$sizes) {
			foreach ($sizes as $size=>$sizeData) {
				$this->oldImages[$attr][$size]['path'] = self::UPLOAD_PATH . $this->oldImages[$attr][$size]['path'];
			}
		}
	}	
	
	
    public function beforeSave($event)
    {
		$ownerName = StringHelper::basename(get_class($this->owner));
		$config = Config::getInstance()->getData();
		
		foreach($this->attributeName as $attrName){
			if (isset($config[$ownerName]) && isset($this->post[$attrName])) {
				
				$attributeConfig = $config[$ownerName][$attrName];
				
				foreach ($attributeConfig['sizes'] as $size=>$sizeConf) {
				
					$attributeConfig = $config[$ownerName][$attrName];
					
					$image64 = $this->post[$attrName][$attrName.'_'.$size];
					$this->setImageData($attrName, $attrName.'_'.$size, $size, $image64);
					
					if ($attributeConfig['i18n']) {
						 foreach (array_keys(Yii::$app->params['otherLanguages']) as $lang) {
							$image64 = $this->post[$attrName][$attrName.'_'.$size.'_'.$lang];
							$this->setImageData($attrName, $attrName.'_'.$size.'_'.$lang, $size, $image64, $lang);
						 }						
					}
				}
			}
        }
		
		$oldImageFiles = $this->getImageLinks($this->oldImages);
	
		$mergeImages = $this->oldImages;
		foreach ($config[$ownerName]  as $attribute=>$attributeConfig) {
			foreach ($attributeConfig['sizes'] as $size=>$sizeConfig) {
				if (isset($this->newImages[$attribute][$size])) {
					$mergeImages[$attribute][$size] = $this->newImages[$attribute][$size];
				} 
	
				if (isset($mergeImages[$attribute][$size])) {
					$align = $attribute.'_'.$size.'_align';
					$mergeImages[$attribute][$size]['align'] = isset($this->post[$attribute][$align])?$this->post[$attribute][$align]:'';
					$background_size = $attribute.'_'.$size.'_background_size';
					$position 		 = $attribute.'_'.$size.'_position';
					$mergeImages[$attribute][$size]['background_size'] = isset($this->post[$attribute][$background_size])?$this->post[$attribute][$background_size]:'';
				
					if (isset($this->post[$attribute][$position])) {
						$mergeImages[$attribute][$size]['position'] = $this->post[$attribute][$position];
					}
				}
				
				if ($attributeConfig['i18n']) {
					 foreach (array_keys(Yii::$app->params['otherLanguages']) as $lang) {
						$attr = $attribute . '_' . $lang;
						if (isset($this->newImages[$attr][$size])) {
							$mergeImages[$attr][$size] = $this->newImages[$attr][$size];
						}	

						if (isset($mergeImages[$attr][$size])) {
							$align = $attribute.'_'.$size.'_'.$lang.'_align';
							$mergeImages[$attr][$size]['align'] = isset($this->post[$attribute][$align])?$this->post[$attribute][$align]:'';													
							$background_size = $attribute.'_'.$size.'_'.$lang.'_background_size';
							$mergeImages[$attr][$size]['background_size'] = isset($this->post[$attribute][$background_size])?$this->post[$attribute][$background_size]:'';													
						}
 					 }
				}
			}
		}
	
		if (!empty($mergeImages)) {
			foreach ($mergeImages as $attributeImage => $attributeData) {
				if (!empty($attributeData)) {
					$this->owner->{$attributeImage} = json_encode($attributeData);
				}
			} 
		}
		
		$allImageFiles = $this->getImageLinks($mergeImages);
		
		//delete files old images
		foreach ($oldImageFiles as $oldFile) {
			if (!in_array($oldFile, $allImageFiles)) {
				self::deleteImage($oldFile);
			}
		}
		
		foreach($this->attributeName as $attrName){
			if (isset($config[$ownerName])) {
				foreach ($config[$ownerName] as $attr=>$data) {
					$this->owner->$attr = str_replace('\/upload\/', '', $this->owner->$attr);
				}
			}
		}
    }
	
	private function getImageLinks($data) {
		$links = [];
		foreach ($data as $attr=>$attrData) {
			foreach ($attrData as $size=>$sizeData) {
				if (isset($data[$attr][$size]['path']))
					$links[] = $data[$attr][$size]['path'];
			}
		}

		return $links;
	}

    public function beforeDelete($event)
    {
		foreach($this->attributeName as $attrName){
            if (!empty($this->owner->$attrName)) {
				$sizes = json_decode($this->owner->$attrName, 1);
				
				foreach ($sizes as $data) {
					self::deleteImage($data['path']);
				}
            }
        }
    }

    public static function deleteImage($path)
    {
		if (strpos($path, self::UPLOAD_PATH) !== 0) {
			$path = self::UPLOAD_PATH . $path;
		}
		
		$file = Yii::getAlias('@webroot') .  $path;
			
		if (is_file($file)) {
			$pathinfo = pathinfo($file);

			//Удаляем тгумби
			$thumbFiles=glob($pathinfo['dirname'] . '/c_*_' . $pathinfo['basename']) + glob($pathinfo['dirname'] . '/r_*_' . $pathinfo['basename']);
			if(is_array($thumbFiles)){
                foreach ($thumbFiles as $f) {
                    @unlink($f);
				}
            }	
		
			@unlink($file);
			
			// если нет файлом удаляем папку
			if (!count(glob($pathinfo['dirname'] . '/*'))) {
				rmdir($pathinfo['dirname']);
			}
			// если нет файлом удаляем папку
			if (!count(glob($pathinfo['dirname'] . '/../*'))) {
				@rmdir($pathinfo['dirname'] . '/../');
			}
		}
	}

    /*
     * Проверяет допустимо ли использовать поведение в текущем сценарии
     */
    public function checkScenario()
    {
        return true;
        #return in_array($this->owner->scenario, $this->scenarios);
    }

    /**
     * @param $attr_name - имя атрибута изображения
     * @param int $width - ширина изображения
     * @param int $height - высота изображения
     * @param $dimension - соотношение сторон
     * @return string
     */
    public function makeThumbnail($attr_name,$width = 0, $height = 0,$dimension, $mode='resize')
    {
		if ($dimension=='')
			$dimension = 2;
	
        $quality = 100;
        $width = $width === 0
            ? $height
            : $width;

        $height = $height === 0
            ? $width
            : $height;
        $image=$this->owner->$attr_name;
        $basePath=Yii::getAlias('@webroot')."/";
        if (!file_exists( $basePath. $image) || $image==null)
            return null;

        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $pos = strrpos($image, "/");
		$fileMode = $mode=='resize'?'':'crop';
        $file = substr($image,0,$pos).'/thumb_cache_' . $width . 'x' . $height . '_' . pathinfo($image, PATHINFO_FILENAME) . "$fileMode." . $ext;

        if (!file_exists($basePath . $file) && file_exists(Yii::getAlias('@webroot') . $file) === false) {
            $thumb = Yii::$app->image->load($basePath . $image)->quality($quality);
			if ($mode=='resize')
				$thumb->resize($width, $height,$dimension);
			else 
				$thumb->crop($width, $height,$dimension);
            $thumb->save($basePath . $file,FALSE);
        }

        return $file;
    }
	
	public function getImageSize($binary_data)
	{
		$expl = explode(';base64,', $binary_data);
		$content = str_replace(' ', '+', $expl[1]);
		$content = base64_decode($content);		
		
		$size = getimagesizefromstring($content);
		
		return array(
			'width' => $size[0],
			'height' => $size[1],
		);
	}
	
	public static function getAlignList()
	{
		return [
			1 => 'left top',
			2 => 'left center',
			3 => 'left bottom',
			4 => 'right top',
			5 => 'right center',
			6 => 'right bottom',
			7 => 'center top',
			8 => 'center',
			9 => 'center bottom',
		];
	}	
	
	public static function getAlignTitle($value)
	{
		$list = self::getAlignList();
		if (isset($list[$value])) {
			return $list[$value];
		} else {
			return 'center top';
		}
 	}	
	
	public static function getBackgroundSizeList()
	{
		return [
			1 => 'auto',
			2 => 'cover',
			3 => 'contain',
		];
	}	
	
	public static function getBackgroundSizeTitle($value)
	{
		$list = self::getBackgroundSizeList();
		if (isset($list[$value])) {
			return $list[$value];
		} else {
			return 'auto';
		}
 	}	
	
}
