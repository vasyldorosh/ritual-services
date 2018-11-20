<?php

namespace app\modules\vacancy;

class Module extends \yii\base\Module
{
    use \app\widgets\ModuleSettingsTrait;
    
    public $title = 'Вакансии';
    
    public $controllerNamespace = 'app\modules\vacancy\controllers';

    public function init()
    {
        parent::init();
        $this->registerSettings($this->id);
    }
}
