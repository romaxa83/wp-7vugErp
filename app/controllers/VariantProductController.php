<?php

namespace app\controllers;

use Yii;
use app\models\CharacteristicValue;
use app\models\Product;
use app\models\VProduct;
use app\controllers\BaseController;
use yii\filters\AccessControl;
use yii\helpers\Json;
use app\controllers\AccessController;

class VariantProductController extends BaseController
{
    public function behaviors() 
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    AccessController::getAccessRules(Yii::$app->controller->id),
                    [
                        'actions' => ['create-variant', 'save-variant-product', 'get-var-prod'],
                        'allow' => true,
                        'roles' => ['product_create'],
                    ],
                    [
                        'actions' => ['change-publish-status'],
                        'allow' => true,
                        'roles' => ['product_update'],
                    ]
                ]
            ]
        ];
    }
    /** 
     * метод подготавливает массив для генераций комбинации
     * @param array $arr массив характеристик
     * @return array возращает массив подготовленый для генераций комбинации
    */
    private function ArrayPreparation($arr)
    {
        $z = 0;
        $a = 0;
        $group = [];
        for($i=0;$i<count($arr['char']);$i++){
            $key = key($arr['char']);
            $nextKey = next($arr['char']);
            $group[$z][$a][$arr['char'][$key]['key']] = $arr['char'][$key]['value'];
            if($arr['char'][$key]['key'] == $nextKey['key']){
                $a++;
            }else{
                $a = 0;
                $z++;
            }
        }
        return $group;
    }
    /**
     * Метод принимает значения товара и характеристики генерирует вариаций
     * входные данные 
     * $post['char'] - характеристики 
     * $post['data'] - массив с информацией про товар 
     * $post['data']['name'] - имя товара 
     * $post['data']['id'] - индефикатор товара 
     * $post['data']['price1'] - цена для магазина с типом 1 
     * $post['data']['price2'] - цена для магазина с типом 2
     * $post['data']['amount'] - количество баового товара 
     * @return json закодирована в json отрендериная view
    */
    public function actionCreateVariant()
    {
        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $groupBack = [];
            //поготавливаем массив
            $group = $this->ArrayPreparation($post);
            //генерируем комбинаций
            $group = VProduct::СombinationOfCharacteristics($group);
            //форматируем комбинаций
            $group = VProduct::CreateGroupFromCombination($group);
            if(!empty($post)){
                foreach ($group as $one){
                    foreach($one as $item){
                        $groupBack['char_name'][] = CharacteristicValue::find()->select(['name'])->where(['id'=>$item])->asArray()->one();
                    }
                    $groupBack['char_value'][] = serialize($one);
                }
                $groupBack['product_name'] = $post['data']['name'];
                $groupBack['product_id'] = $post['data']['id'];
                $groupBack['price1'] = $post['data']['price1'];
                $groupBack['price2'] = $post['data']['price2'];
                $groupBack['amount'] = $post['data']['amount'];
                $groupBack['countProd'] = count($groupBack['char_value']);
                $groupBack['countChars'] = count($groupBack['char_name']);
                $groupBack['variant_amount'] = VProduct::GetAmountForOneProd($groupBack['countProd'],$post['data']['amount']);
                return JSON::encode($this->renderAjax('table-variant', compact('groupBack')));
            }
        }
    }
    /**
     * Метод принимает значения вариативного товара в случае уникальности вариативного товара записует его в бд в обратном обновляет существующий 
     * входные данные
     * $post['product_id'] - индефикатор базового товара 
     * $post['char_value'] - характеристики 
     * $post['vendor_code'] - вендор код базового товара
     * @return mixed 
    */
    public function actionSaveVariantProduct()
    {
        $post = Yii::$app->request->post();
        if(!empty($post) && !empty($post['char_value'])){
            $product = Product::findOne($post['product_id']);
            if ($product->is_variant == 2){
                $chars = VProduct::GetUniqueChar_value($post['char_value'],$post['product_id']);
                if(isset($chars['old'])){
                    $old = $chars['old'];
                    unset($chars['old']);
                }
            }else{
                $product->is_variant = 2;
                $chars = $post['char_value'];
                $product->update();
            }
            $post['vendor_code'] = $product->vendor_code;
            if(!empty($chars) || !empty($old)){
                $error = VProduct::saveVariantProduct($chars,$post, !empty($old) ? $old : '' );
                if(!empty($error)){
                    ShowMessenge($error);
                }else{
                    ShowMessenge('Вариативный продукт создан удачно','success');
                }
            }
        }
        return $this->redirect(Yii::$app->request->referrer);
    }
    /**
     * Получения вариативных товаров по базовому товару
     * входные данные 
     * $prod_id - индефикатор базового товара
    */
    public function actionGetVarProd()
    {
        if (Yii::$app->request->isAjax) {
            $prod_id = Yii::$app->request->post('value');
            $prod = Product::findOne($prod_id);
            $is_disable = ($prod->publish_status !== 1);
            $v_product = VProduct::find()->where(['product_id'=>$prod_id])->asArray()->all();
            foreach($v_product as $oneProduct){
                $chars = VProduct::getCharValueFromId($oneProduct['char_value']);
                $arr['chars'] = $chars;
                $arr['price1'] = $oneProduct['price1'];
                $arr['price2'] = $oneProduct['price2'];
                $arr['amount'] = $oneProduct['amount'];
                $arr['product_id'] = $oneProduct['product_id'];
                $arr['id'] = $oneProduct['id'];
                $arr['publish_status'] = $oneProduct['publish_status'];
                $arr['disable'] = $is_disable;
                $product[] = $arr;
                $arr = [];
            }
            return $this->renderAjax('variant_product',compact('product'));
        }
    }
    /**
     * Изменения статус для магазина модели Product работает по Ajax
     * так же отправляет данные на магазин просто выгодно
     * принимает 
     * $data['id'] - индефикатор модели 
     * $data['status'] - 1/0 (включен/выключен)
    */
    public function actionChangePublishStatus($id = null,$status = null)
    {
        $data = Yii::$app->request->post();
        $product = VProduct::findOne(($id == null) ? $data['id'] : $id);
        $product->publish_status = ($status == null) ? $data['status'] : $status;
        $product->update();
    }
}