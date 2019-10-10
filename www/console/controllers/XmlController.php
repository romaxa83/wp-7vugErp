<?php

namespace app\console\controllers;

use app\modules\xml\service\XmlMarketService;
use app\modules\xml\type\ShopInfo;
use app\repositories\CategoryRepository;
use app\repositories\ProductRepository;
use yii\console\Controller;
use yii\helpers\Console;

date_default_timezone_set('Europe/Kiev');

class XmlController extends Controller
{
    /**
     * Обновляет xml-shop.
     * @package app\commands
     */
    public function actionUpdate()
    {
        if(\Yii::$app->cache->exists('shop-xml')){
            \Yii::$app->cache->delete('shop-xml');
        }
        $xml = $this->generateXml(true);

        \Yii::$app->cache->set('shop-xml',$xml);

        $this->stdout('xml обновлена и помещена в кеш,просмотр по пути /xml-shop.xml' . PHP_EOL,Console::FG_GREEN);
    }

    /**
     * Генерирует xml-shop(появиться по пути \shop-xml.xml).
     * @package app\commands
     */
    public function actionGenerate()
    {
        if(\Yii::$app->cache->exists('shop-xml')){
            \Yii::$app->cache->delete('shop-xml');
        }
        $xml = $this->generateXml();

        \Yii::$app->cache->set('shop-xml',$xml);

        $this->stdout('xml сгенерированы и помещена в кеш,просмотр по пути /xml-shop.xml' . PHP_EOL,Console::FG_GREEN);
    }

    private function generateXml($update = false)
    {
        return (new XmlMarketService(
            new ShopInfo(\Yii::$app->name,\Yii::$app->name),
            new CategoryRepository(),
            new ProductRepository()))
            ->generate($update);
    }
}