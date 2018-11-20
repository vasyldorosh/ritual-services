<?php

namespace app\components\imageAdaptive;

use Yii;
use yii\base\Widget;

class Manager extends Widget {

	public $i18nAttribute = false;	
	public $model;
	public $attribute;
	public $attributeConfig;
	
    public $htmlOptions = [];
	
    public function run() {
						
        $this->render('imageAttributeManager', array(
			'model' => $this->model,
			'attribute' => $this->attribute,
			'attributeConfig' => $this->attributeConfig,
		));
    }

	public static function getAttributesData($modelName)
	{
		$configs = Config::getInstance()->getData();
		
		if (isset($configs[$modelName])) {
			$data = [];
			foreach ($configs[$modelName] as $attribute=>$attributeConfig) {
				$data[$attribute] = $attributeConfig;
			}
			
			return $data;
		} else {
			return [];
		}
	}
	
	public static function getAttributes($modelName, $i18n=false)
	{
		$configs = Config::getInstance()->getData();
		if (isset($configs[$modelName])) {
			$attributes = [];
			foreach ($configs[$modelName] as $attribute=>$attributeConfig) {
				$attributes[] = $attribute;
				if ($i18n && $attributeConfig['i18n']) {
					foreach (Yii::$app->params['otherLanguages'] as $lang => $title) {
						$attributes[] = $attribute . '_' . $lang;
					}
				}
			}
			
			return (array)$attributes;
		} else {
			return [];
		}
	}
	
	public static function getI18nAttributes($modelName)
	{
		$configs = Config::getInstance()->getData();
		if (isset($configs[$modelName])) {
			$attributes = [];
			foreach ($configs[$modelName] as $attribute=>$attributeConfig) {
				if ($attributeConfig['i18n'])
					$attributes[] = $attribute;
			}
			
			return $attributes;
		} else {
			return [];
		}
	}
	
	public static function img($images, $options=[]) {
		$html = '';
		
		if (!empty($images) && is_array($images)) {
			$html.= '';
           
            foreach ($images as $minWidth=>$data) {
                $src = $data[0];
                $html.= '<source srcset="'.$src.'" media="(min-width: '.$minWidth.'px)">';
				$itemClass=isset($options['itemClass']) ? $options['itemClass'] : '';
                $html.= '<span data-src="'.$src.'" data-media="(min-width: '.$minWidth.'px)" class="'.$itemClass.'"></span>';
            }
        }
		
		return $html;
	}
	
	public static function back($images, $attr='data-image-breakpoints') {
		$html = '';
		if (!empty($images) && is_array($images)) {
			$json = json_encode($images);
			$json = str_replace('\/','/', $json);
		    $html = "{$attr}='".$json."'";
		}
		return str_replace(['[[', ']]'], ['[', ']'], $html);
	}
	
	public static function getJson($images)
	{
		$json = '';
		if (!empty($images) && is_array($images)) {
			$images[100000] = end($images);
			$json = json_encode($images);
			$json = str_replace('\/','/', $json);
		}
		return $json;		
	}
	
	public static function getImageTypes($modelName) 
	{
		$configs = Config::getInstance()->getData();
		if (isset($configs[$modelName])) {
			$data = [];
			foreach ($configs[$modelName] as $attribute=>$attributeConfig) {
				$data[$attribute] = $attributeConfig['label'];
			}
			
			return $data;
		} else {
			return [];
		}					
	}

	public static function back_one($model, $map, $attr='data-image-breakpoints') {
		$html = '';
		
		$images = [];
		if (!empty($model->image)) {
			foreach ($map as $size=>$data) {
				$images[$data['minWidth']] = [$model->getImageUrl('image', $data['width'], $data['height'], 'crop'), '', ''];
			}
		}
		
		if (!empty($images) && is_array($images)) {
			$json = json_encode($images);
			$json = str_replace('\/','/', $json);
		    $html = "{$attr}='".$json."'";
		}
		return str_replace(['[[', ']]'], ['[', ']'], $html);
	}

	
}