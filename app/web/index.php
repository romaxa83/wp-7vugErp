<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'prod');

//if (YII_ENV == 'prod') {
//    $_SERVER['SERVER_PORT'] = 443; $_SERVER['HTTPS'] = 'on'; if(isset($_SERVER['HTTP_X_REAL_IP'])) $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP']; $_SERVER['SERVER_PORT'] = 443;
//}

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';


/*
$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../config/web.php',
    require __DIR__ . '/../config/web-local.php'
);*/
(new yii\web\Application($config))->run();
