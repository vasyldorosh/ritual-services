<?php

namespace app\modules\slider;

class Module extends \yii\base\Module
{
    use \app\widgets\ModuleSettingsTrait;
    
    public $title = 'Слайдер';
    
    public $controllerNamespace = 'app\modules\slider\controllers';

    public function init()
    {
        parent::init();
        $this->registerSettings($this->id);
    }
}
