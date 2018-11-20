<?php
define('URL_TRANSFORMER_DYNAMIC', 'dinamic');
$skip = array('.', '..');
$modules = [];
$files = scandir(dirname(__FILE__) . '/../modules');
foreach($files as $file)
    if(!in_array($file, $skip) && is_dir(dirname(__FILE__) . '/../modules/'.$file))
        $modules[$file] = ['class' => 'app\modules\\'.$file.'\Module'];
    
$config = [
    'id' => 'acms',
    'name' => 'ACMS 9',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ua',
    'sourceLanguage' => 'ua',
    'modules' => $modules,
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '5bdV2clPQw7gpZ-Vt6yI5yNVQMXBZUkw',
            'baseUrl' => '',
            'enableCsrfValidation' => false,
        ],
        'cache' => [
            'class' => 'yii\caching\MemCache',
			'keyPrefix' => 'workmarket',
        ],
        'user' => [
            'identityClass' => 'app\modules\admin\models\Admin',
			'class' => 'yii\web\User',
            'enableAutoLogin' => true,
			'loginUrl' => ['admin/auth/login'],
			'idParam' => '__id_admin',
        ],
        'image' => [
            'class' => 'app\components\image\ImageComponent',
        ],
        'event' => [
            'class' => 'app\components\Event',
        ],
        'email' => [
            'class' => 'app\components\Email',
        ],		
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],       
        'urlManager' => [
            'enablePrettyUrl' => false,
			'showScriptName' => true,
            'rules' => [
                'acms' => 'admin/admin/login',
                '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
                '' => 'site/index',
                '<action>'=>'site/<action>'
            ],
        ],
        'assetManager' => [
            'basePath' => '@webroot/assets',
			'forceCopy' => YII_DEBUG,     
            'baseUrl' => '@web/assets',
        ],
		'i18n' => [
			'translations' => [
				'app' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'basePath' => '@app/messages',
					'sourceLanguage' => 'ru',
					'fileMap' => [
						'app' => 'app.php',
					],
				],			
			],
		],		
        'db' => require(__DIR__ . '/db.php'),		
		
		// (optionally) you can configure logging
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@app/runtime/eauth.log',
                    'categories' => ['nodge\eauth\*'],
                    'logVars' => [],
                ],
            ],
        ],		
	
		
    ],
    'params' => require(__DIR__ . '/params.php'),
];

if (YII_DEBUG) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    
	$config['modules']['debug'] = [
		'class'=>'yii\debug\Module',
		'allowedIPs'=> ['*'],
	];	
	
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
	
}

return $config;