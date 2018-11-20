<?php

namespace app\modules\contentblock;

class Module extends \yii\base\Module
{
    use \app\widgets\ModuleSettingsTrait;
    
    public $title = 'Контентные блоки';
    
    public $controllerNamespace = 'app\modules\contentblock\controllers';

    public function init()
    {
        parent::init();
        $this->registerSettings($this->id);
    }
}
