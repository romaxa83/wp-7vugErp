<?php

namespace app\controllers;

use app\models\Agent;
use app\models\Category;
use app\models\Product;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use Yii;
use yii\filters\AccessControl;
use app\controllers\AccessController;
use app\controllers\BaseController;

class ChartController extends BaseController
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
                        'roles' => ['admin'],
                    ]
                ]
            ]
        ];
    }
    
    public function actionIndex() 
    {
        if(Yii::$app->user->isGuest){
            return Yii::$app->getResponse()->redirect('/site/login');
        }
        return $this->render('index');
    }
    //если в $data = 1,строит диаграмму по поставщикам
    //если в $data = 2,строит диаграмму по категориям
    public function actionChartConstruct()
    {
        if(Yii::$app->request->isAjax){
            $data = \Yii::$app->request->post();
            if($data['data'] == 1){
                $agents = ArrayHelper::map(Agent::find()->select(['id','firm'])->where(['type' => 1])->asArray()->all(),'id','firm');
                return Product::BuildChart($agents,'agent');
            }
            if($data['data'] == 2){
                $categories = ArrayHelper::map(Category::find()->select(['id','name'])->where(['status' => 1])->asArray()->all(),'id','name');
                return Product::BuildChart($categories,'category');
            }
        }
    }

    public function actionTableProductForAgent($type)
    {
        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            if(empty($post)){
                $products = Product::find()->asArray()->all();
            }else{
                if($type == 1 ){
                    $id_agent = Agent::find()->select('id')->where(['firm' => $post['name']])->asArray()->one();
                    $products = Product::find()->where(['agent_id' => $id_agent['id']])->asArray()->all();
                }else{
                    $id_category = Category::find()->select('id')->where(['name' => $post['name']])->asArray()->one();
                    $products = Product::find()->where(['category_id' => $id_category['id']])->asArray()->all();
                }
            }
            $new_product = [];
            foreach($products as $id => $product){
                $all_cost_price = $product['cost_price'] * $product['amount'];
                $all_price1 = $product['price1'] * $product['amount'];
                $all_price2 = $product['price2'] * $product['amount'];
                $new_product[$id]['all_cost_price'] = number_format($all_cost_price,2);
                $new_product[$id]['all_price1'] = $all_price1;
                $new_product[$id]['all_price2'] = $all_price2;
                $new_product[$id]['name'] = $product['name'];
                $new_product[$id]['amount'] = $product['amount'];
                $new_product[$id]['unit'] = $product['unit'];
                $new_product[$id]['start_price'] = $product['start_price'];
                $new_product[$id]['cost_price'] = number_format($product['cost_price'],2);
                $new_product[$id]['trade_price'] = $product['trade_price'];
                $new_product[$id]['price1'] = $product['price1'];
                $new_product[$id]['price2'] = $product['price2'];
            }
            return JSON::encode($new_product);
        }
    }
}