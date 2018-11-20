<?php

namespace app\modules\settings;

class Module extends \yii\base\Module
{
    use \app\widgets\ModuleSettingsTrait;
    
    public $title = 'Настойки';
    
    public $controllerNamespace = 'app\modules\settings\controllers';

    public function init()
    {
        parent::init();
        $this->registerSettings($this->id);
    }
}
