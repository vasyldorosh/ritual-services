<?php

namespace app\modules\event;

class Module extends \yii\base\Module
{
    use \app\widgets\ModuleSettingsTrait;
    
    public $title = 'События';
    
    public $controllerNamespace = 'app\modules\event\controllers';

    public function init()
    {
        parent::init();
        $this->registerSettings($this->id);
    }
}
