<?php
namespace app\widgets;

use Yii;
use yii\helpers\Json;
use yii\imagine\Image;

class ImagesHelper
{
    /**
     * @param string|null $img название файла изображения, при отсутствии создается с noimage.jpg, 
     *        если отсутствует превю с указанными размерами оно создается в папке '/images/products/'
     * @param intager $height высота изображения
     * @param intager $width ширина изображения, если 0 отдается исходное изображение
     * @param boolen $size указывается ширина блока в стилях если true
     *
     * @return string изображение как бекграунд блока
     */
    public function render_asDiv($img, $height, $width, $size = false)
    {
        $title = $img == null ? '/images/noimage_' : '/images/products/'.$img;
        $ext = $img == null ? '.jpg' : '.'.substr(strrchr($img, '.'), 1);
            
        $file = Yii::getAlias('@webroot').$title.$width.'x'.$height.$ext;
        
        $source = Yii::getAlias('@webroot/images').'/'.($img == null ? 'noimage.jpg' : 'products/'.$img);
            
        if (!is_file($file) && is_file($source) && ($height > 0 && $width > 0))
             Image::thumbnail($source, $height, $width)->save($file, ['quality' => 80]);
               
        return '<div class="image-block" style="height:'.$height.'px;'.($size ? 'width:'.$width.'px;' : '').
               'background-image:url('.($img == null && $width == 0 ? '/noimage.jpg' : $title.($width > 0 ? $width.'x'.$height.$ext : '')).');"></div>';
    }
    
    /**
     * @param array $data массив названий файлов изображений, 
     *        если отсутствует превю с указанными размерами оно создается в папке '/images/products/'
     * @param intager $height высота изображения
     * @param intager $width ширина изображения
     * @param bool $prod изображение для каталога товаров
     *
     * @return array|false изображения как массив тегов <img />
     */
    public function render_asTag($data, $height, $width, $prod = false)
    {
        if(!empty($data) && is_array($data))
        {
            foreach($data as $one)
            {
                $title = Yii::getAlias('@webroot/images/products').'/'.$one;
            
                $ext = '.'.substr(strrchr($one, '.'), 1);
            
                $file = $title.$width.'x'.$height.$ext;
            
                if (!is_file($file) && is_file($title) && ($height > 0 && $width > 0))
                    Image::thumbnail($title, $height, $width)->save($file, ['quality' => 80]);
                
                if ($prod)
                {
                    $preview = '/images/products/'.$one.$width.'x'.$height.$ext;
                    break;
                }
                else
                    $preview[] = '<img src="/images/products/'.$one.$width.'x'.$height.$ext.'" />';
            }
            return $preview;
        }
        else
            return false;
    }
    
    /**
     * @param array $data массив названий файлов изображений
     * @param intager $width ширина изображения
     * @param string $del_url ссылка для удаления изображения
     * @param string $id товар которому принадлежит изображение
     *
     * @return array|false двомерный массив настроек модуля \dosamigos\fileinput\BootstrapFileInput (свойство initialPreviewConfig)
     *         вывод превью изображений из БД
     */
    public function previewConfig($data, $width, $del_url, $id)
    {
        if(!empty($data) && is_array($data))
        {
            foreach($data as $one)
                $result[] = ['caption' => $one, 'width' => $width.'px', 'url' => $del_url, 'key' => $one, 'extra' => ['id' => $id]];
            
            return $result;
        }
        else
            return false;
    }
    
    /**
     * Записывает изображения в папку '/images/products/'
     * 
     * @param array $photos файлы существующие в БД
     * @param array $images файлы для загрузки
     *
     * @return string массив в формате JSON для записи в БД
     */
    public function upload($photos = '', $images)
    {
        if(!file_exists(Yii::getAlias('@webroot/images/products')))
            mkdir(Yii::getAlias('@webroot/images/products'), 0777);
                
        if($images)
        {
            foreach ($images as $file)
            {
                $title = Yii::$app->getSecurity()->generateRandomString(10) . '.' . $file->extension;
                $file->saveAs(Yii::getAlias('@webroot/images/products') . '/' . $title);
                $images_title[] = $title;
            }
        }
        if(!empty($photos) && isset($images_title))
            $images = Json::encode(array_merge($photos, $images_title));
        elseif(isset($images_title))
            $images = Json::encode($images_title);
        elseif(!empty($photos))
            $images = Json::encode($photos);
        else
            $images = '';
            
        return $images;
    }
    
    /**
     * Удаляет из папки пользователя переданные изображения и их превью
     * 
     * @param array $images названия файлов
     */
    public function delete($images)
    {
        foreach($images as $one)
        {
            $filepath = Yii::getAlias('@webroot').'/images/products/';
                
            foreach (glob($filepath.$one.'*') as $file) 
            {
                if(is_file($file))
                    unlink($file);
            }
        }
    }
}