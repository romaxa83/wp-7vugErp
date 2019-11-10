<?php

namespace app\modules\xml\controllers;

use yii\base\Module;
use yii\helpers\Url;
use yii\web\Controller;
use app\modules\xml\service\XmlMarketService;
use yii\filters\AccessControl;
use app\controllers\AccessController;

class XmlMarketController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    AccessController::getAccessRules(Yii::$app->controller->id),
                    [
                        'allow' => true,
                        'roles' => ['admin']
                    ]
                ]
            ]
        ];
    }
    /**
     * @var XmlMarketService
     */
    private $generator;

    public function __construct($id, Module $module, XmlMarketService $generator, array $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->generator = $generator;
    }

    public function actionIndex()
    {
        if (\Yii::$app->cache->exists('shop-xml')){
            $xml = \Yii::$app->cache->get('shop-xml');
        } else {
            $xml = $this->generator->generate();
        }

        return \Yii::$app->response->sendContentAsFile($xml,'xml-shop.xml',[
            'mimeType' => 'application/xml',
            'inline' => true
        ]);
    }
}