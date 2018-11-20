<?php

namespace app\widgets;

use Yii;

/**
 * ModuleSettingsTrait подключение файлов перевода в модулях.
 * Использование в модуле echo Yii::t('modules/ID модуля/main', 'Сообщение');
 */
trait ModuleSettingsTrait
{
    public function registerSettings($module)
    {
        //Переопределение обработчика ошибок
        Yii::$app->errorHandler->errorAction = 'admin/admin/error';
        
        Yii::$app->i18n->translations['modules/'.$module.'/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => Yii::$app->sourceLanguage,
            'basePath' => '@app/modules/'.$module.'/messages',
            'fileMap' => [
                'modules/'.$module.'/main' => 'main.php'
            ]
        ];
    }
}