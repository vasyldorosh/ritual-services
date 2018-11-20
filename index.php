<?php
if ($_SERVER['SERVER_NAME'] == 'fxnewsreader.loc') {
	error_reporting(E_ALL);
} else {
	error_reporting(0);
}

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', 0);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');

$application = new yii\web\Application($config);

include('../helpers/functions.php');

$application->run();
