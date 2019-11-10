<?php

use developeruz\db_rbac\behaviors\AccessBehavior;

$params = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);
$db = require(__DIR__ . '/db.php');

$config = [
    'id' => 'basic',
    'name' => '7 выгод',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'app\bootstrap\SetUp'
    ],
    'language' => 'ru_RU',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules' => [
        'webshell' => [
            'class' => 'samdark\webshell\Module',
            'allowedIPs' => ['*'],
            'checkAccessCallback' => function (\yii\base\Action $action) {
                if($app = Yii::$app){
                    if($app->request->pathInfo === 'webshell/default/rpc'){
                        return true;
                    }
                    if(isset($app->request->queryParams['token'])
                        && !empty($app->request->queryParams['token'])
                        && ($app->request->queryParams['token'] === Yii::$app->params['webshellToken'])){
                        return true;
                    }
                }
            }
        ],
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
        'order' => [
            'class' => 'app\modules\order\Order',
        ],
        'xml' => [
            'class' => 'app\modules\xml\Xml',
        ],
        'manager' => [
            'class' => 'app\modules\manager\Manager',
//            'as access' => [ // if you need to set access
//                'class' => 'yii\filters\AccessControl',
//                'rules' => [
//                    [
//                        'controllers'=>['manager/manager'],
//                        'actions' => [''],
//                        'allow' => true,
//                        'roles' => ['manager']
//                    ],
//                    [
//                        'actions' => ['*'],
//                        'allow' => false,
//                        'roles' => ['manager']
//                    ],
//                ]
//            ],
        ],
    ],
    'controllerMap' => [
        'export' => 'phpnt\exportFile\controllers\ExportController'
    ],
    'components' => [
//        'assetManager' => [
//            'bundles' => [
//                'nullref\datatable\DataTableAsset' => [
//                    'styling' => \nullref\datatable\DataTableAsset::STYLING_DEFAULT,
//                ]
//            ],
//        ],
        'assetManager' => [
            'appendTimestamp' => true,
            'forceCopy' => true
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'best',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
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
    ],
    'as AccessBehavior' => [
        'class' => AccessBehavior::className(),
        'protect' => ['site/about'],
        'rules' => [
//             'site' => [
//                 [
//                     'actions' => ['index'],
//                     'allow' => true,
//                 ],
//             ],
        ]
    ],
    'params' => $params,
    'defaultRoute' => 'site/index',
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
            // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
            // uncomment the following to add your IP if you are not connecting from localhost.
            'allowedIPs' => ['*'],
    ];
}

return $config;
