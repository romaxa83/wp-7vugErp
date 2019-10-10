<?php

$params = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\console\controllers',
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'logger' => [
            'class' => app\modules\logger\Module::class,
            // Список моделей которые логировались
            'entityMap' => [
                'product' => app\models\Product::class,
                'operations' => app\models\Operations::class,
                'access' => app\models\Access::class
            ],
        ],
        'activityLogger' => [
            'class' => app\modules\logger\Manager::class,
            //'enabled' => YII_ENV_PROD, //Включаем логирование для {value} версии
            'deleteOldThanDays' => 1, // clean() будут удалены все данные добавленные {value} дней назад
            'user' => 'user',
            'userNameAttribute' => 'username',
            'storage' => 'activityLoggerStorage',
            'messageClass' => [
                'class' => app\modules\logger\LogMessage::class,
            //'env' => 'console',
            ],
        ],
        'activityLoggerStorage' => [
            'class' => app\modules\logger\DbStorage::class,
            'tableName' => '{{%activity_log}}',
            'db' => 'db',
        ],
        'user' => null,
        'session' => [ // for use session in console application
            'class' => 'yii\web\Session'
        ],
        'db' => $db,
    ],
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
