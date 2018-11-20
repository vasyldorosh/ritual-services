<?php

require(__DIR__ . '/../helpers/functions.php');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

return [
    'id' => 'acms',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ua',
    'sourceLanguage' => 'ua',	
    'controllerNamespace' => 'app\commands',
    'components' => [
		'cache' => [
            'class' => 'yii\caching\MemCache',
			'keyPrefix' => 'workmarket',
        ],
        'email' => [
            'class' => 'app\components\Email',
        ],		
        'db' => $db,
    ],
    'params' => $params,
];
