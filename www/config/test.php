<?php

use developeruz\db_rbac\behaviors\AccessBehavior;

$params = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);
$db = require(__DIR__ . '/test-db.php');

$config = [
    'id' => 'test_app',
    'basePath' => dirname(__DIR__),
    'language' => 'en_EN',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules' => [
        'permit' => [
            'class' => 'developeruz\db_rbac\Yii2DbRbac',
            'params' => [
                'userClass' => 'app\models\User',
                'accessRoles' => ['admin'],
            ]
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
        'xml' => [
            'class' => 'app\modules\xml\Xml',
        ],
        'manager' => [
            'class' => 'app\modules\manager\Manager',
        ],
    ],
    'controllerMap' => [
        'export' => 'phpnt\exportFile\controllers\ExportController'
    ],
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'class' => 'yii\web\User',
            'enableAutoLogin' => true
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,
            'rules' => [
                ['pattern' => 'shop-xml', 'route' => 'xml/xml-market/index', 'suffix' => '.xml'],
                'api' => 'api/index',
                'api/getCategory' => 'api/category/get-category',
                'api/getCategoryById' => 'api/category/get-category-by-id',
                'api/getProducts' => 'api/product/get-products',
                'api/getVProducts' => 'api/product/get-v-products',
                'api/ping' => 'api/index/ping',
                'manager/' => '/manager/manager/index'
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
        'request' => [
            'enableCookieValidation' => true,
            'cookieValidationKey' => 'xxxxxxx',
    
        ],
    ],
    'params' => $params,
    'defaultRoute' => 'site/index',
];

return $config;