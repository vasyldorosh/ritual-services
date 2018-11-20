<?php

namespace app\modules\product;

class Module extends \yii\base\Module
{
    use \app\widgets\ModuleSettingsTrait;
    
    public $title = 'Товары';
    
    public $controllerNamespace = 'app\modules\product\controllers';

    public function init()
    {
        parent::init();
        $this->registerSettings($this->id);
    }
}
