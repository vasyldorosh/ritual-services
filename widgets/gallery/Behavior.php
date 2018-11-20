
<?php

/**
 * Behavior for adding gallery to any model.
 *
 * @author Bogdan Savluk <savluk.bogdan@gmail.com>
 */
class GalleryBehavior extends CActiveRecordBehavior {

    /** @var string Model attribute name to store created gallery id */
    public $idAttribute;

    /**
     * @var array Settings for image auto-generation
     * @example
     *  array(
     *       'small' => array(
     *              'resize' => array(200, null),
     *       ),
     *      'medium' => array(
     *              'resize' => array(800, null),
     *      )
     *  );
     */
    
    /** @var boolean does images in gallery need names */
    public $name;

    /** @var boolean does images in gallery need descriptions */
    public $description;
    
    //private $_gallery;

    /** Will create new gallery after save if no associated gallery exists */
//    public function beforeSave($event) {
//        parent::beforeSave($event);
//        if ($event->isValid) {
//            if (empty($this->getOwner()->{$this->idAttribute})) {
//                $gallery = new Gallery();
//                $gallery->name = $this->name;
//                $gallery->description = $this->description;
//                $gallery->versions = $this->versions;
//                $gallery->save();
//
//                $this->getOwner()->{$this->idAttribute} = $gallery->id;
//            }
//        }
//    }

    /** Will remove associated Gallery before object removal */
//    public function beforeDelete($event) {
//        if (!empty($this->getOwner()->{$this->idAttribute})) {
//            /** @var $gallery Gallery */
//            $gallery = Gallery::model()->findByPk($this->getOwner()->{$this->idAttribute});
//            $gallery->delete();
//        }
//        parent::beforeDelete($event);
//    }

    /** Method for changing gallery configuration and regeneration of images versions */
    public function changeConfig() {
//        /** @var $gallery Gallery */
//        $gallery = Gallery::model()->findByPk($this->getOwner()->{$this->idAttribute});
//        if ($gallery == null)
//            return;
//        foreach ($gallery->galleryPhotos as $photo) {
//            $photo->removeImages();
//        }
//
//        $gallery->name = $this->name;
//        $gallery->description = $this->description;
//        //$gallery->versions = $this->versions;
//        $gallery->save();

        foreach ($this->galleryPhotos as $photo) {
            $photo->updateImages();
        }

        //$this->getOwner()->{$this->idAttribute} = $gallery->id;
       // $this->getOwner()->saveAttributes($this->getOwner()->getAttributes());
    }

    /** @return Gallery Returns gallery associated with model */
//    public function getGallery() {
//        if (empty($this->_gallery)) {
//            $this->_gallery = Gallery::model()->findByPk($this->getOwner()->{$this->idAttribute});
//        }
//        return $this->_gallery;
//    }

    public function getGalleryPhotos($model,$multilingual=false) {
        $criteria = new CDbCriteria();
        $criteria->condition = 'gallery_id = :gallery_id';
        $criteria->params[':gallery_id'] = $this->getOwner()->{$this->idAttribute};
        $criteria->order = '`rank` asc';
		
		if ($multilingual)
			return CActiveRecord::model($model)->multilingual()->findAll($criteria);
		else 
			return CActiveRecord::model($model)->localized()->findAll($criteria);
    }

}
