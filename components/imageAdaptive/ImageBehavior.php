<?php

namespace app\components\imageAdaptive;

use Yii;
use yii\base\Behavior;
use app\components\behaviors\ImageAdaptiveUploadBehavior;


class ImageBehavior extends Behavior {
	
	const UPLOAD_PATH = '/upload/';
	
	public function getImages($attribute='json_image')
	{
		$configs = Config::getInstance()->getData();
		$configs = $configs[$this->owner->formName()];
		$images = array();
		$data = !empty($this->owner->$attribute)?json_decode($this->owner->$attribute, 1):[];

		foreach ($data as $size=>$sizeData) {
			$path = '/upload/' . $sizeData['path'];
			if (isset($configs[$attribute]['sizes'][$size]) && is_file(Yii::getAlias('@webroot') . $path)) {
				$minWidths = is_array($configs[$attribute]['sizes'][$size]['minWidth'])?$configs[$attribute]['sizes'][$size]['minWidth']:[$configs[$attribute]['sizes'][$size]['minWidth']];
				foreach ($minWidths as $minWidth) {
					$images[$minWidth] = [
						$path, 
						ImageAdaptiveUploadBehavior::getAlignTitle($sizeData['align']), 
						isset($sizeData['background_size']) ? ImageAdaptiveUploadBehavior::getBackgroundSizeTitle($sizeData['background_size']) : '',
					];
				}
			}
		}
		
		return $images;
	}
	
	public function getFirstImage($attribute='json_image')
	{
		$images = $this->getImages($attribute);
		foreach ($images as $szie=>$data) {
			return $data[0];
		}
	}

	public function getImage($size, $attribute='json_image')
	{
		$configs = Config::getInstance()->getData();
		$configs = $configs[get_class($this->owner)];
		$images = array();
		$data = !empty($this->owner->$attribute)?json_decode($this->owner->$attribute, 1):[];
		
		
		if (isset($data[$size]['path']) && is_file(Yii::getAlias('@webroot').$data[$size]['path'])) {
			return $data[$size]['path'];
		}
		
		return false;
	}
	
	public function getImageThumb($attribute, $size, $width, $height, $mode='resize')
	{
		$dimension = 2;
		$quality = 100;
        
		$data = !empty($this->owner->$attribute)?json_decode($this->owner->$attribute, 1):[];
		$sizeDim = "{$width}x{$height}";
		
		if (isset($data[$size]['path'])) {
			$basePath = Yii::getAlias('@webroot');
			$imageFile = $data[$size]['path'];
				
			if (is_file($basePath . self::UPLOAD_PATH . $imageFile)) {
			
				$imagesize = getimagesize($basePath . self::UPLOAD_PATH . $imageFile);
				if ($imagesize[0]==$width && $imagesize[1]==$height) {
					return $data[$size]['path'];
				} else {
					
					$ext = pathinfo($imageFile, PATHINFO_EXTENSION);
					$pos = strrpos($imageFile, "/");
					$fileMode = $mode=='resize'?'r':'c';
					$file = self::UPLOAD_PATH . substr($imageFile,0,$pos).'/'.$fileMode.'_' . $width . 'x' . $height . '_' . pathinfo($imageFile, PATHINFO_FILENAME) . "." . $ext;
					
					if (!is_file($basePath . $file) && is_file($basePath . self::UPLOAD_PATH . $imageFile)) {
						$thumb = Yii::$app->image->load($basePath . self::UPLOAD_PATH . $imageFile)->quality($quality);
						if ($mode=='resize')
							$thumb->resize($width, $height,$dimension);
						else 
							$thumb->crop($width, $height,$dimension);
						$thumb->save($basePath . $file,FALSE);
					}

					return $file;		
				}
			}
		}
		
		return false;
	}

}
