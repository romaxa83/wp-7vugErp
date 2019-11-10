<?php

namespace app\bootstrap;

use app\modules\xml\service\XmlMarketService;
use app\modules\xml\type\ShopInfo;
use yii\base\BootstrapInterface;

class SetUp implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $container = \Yii::$container;

        $container->setSingleton(XmlMarketService::class,[],[
            new ShopInfo(\Yii::$app->name,\Yii::$app->name)
        ]);

    }
}