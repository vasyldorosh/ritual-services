<?php

namespace app\modules\structure;

class Module extends \yii\base\Module
{
    use \app\widgets\ModuleSettingsTrait;
    
    public $title = 'Структура сайта';
    
    public $controllerNamespace = 'app\modules\structure\controllers';

    public function init()
    {
        parent::init();
        $this->registerSettings($this->id);
    }
}
