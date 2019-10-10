<?php

namespace app\controllers\api;

use Yii;
use yii\rest\Controller;
use app\models\Product;
use app\models\VProduct;
use app\models\Characteristic;
use app\models\CharacteristicValue;
use yii\helpers\ArrayHelper;

class ProductController extends Controller {

    /**
     * @SWG\Get(path="/api/getProducts",
     *     tags={"Product"},
     *     summary="Получение всех товаров",
     *     @SWG\Response(
     *         response = 200,
     *         description = "Ok",
     *         @SWG\Schema(ref = "#/")
     *     ),
     *     @SWG\Response(
     *         response = 400,
     *         description = "Bad Request",
     *         @SWG\Schema(ref = "#/")
     *     ),
     *     @SWG\Response(
     *         response = 404,
     *         description = "Not Found",
     *         @SWG\Schema(ref = "#/")
     *     ),
     *     @SWG\Response(
     *         response = 500,
     *         description = "Internal Server Error"
     *     )
     * )
     */
    public function actionGetProducts() {
        $product = Product::find()
                ->select([
                    'product.id', 'category.name AS category_name', 'product.name AS product_name', 'product.category_id', 
                    'product.trade_price', 'product.publish_status', 'v_product.char_value', 'v_product.amount', 'product.unit',
                    'IF(product.is_variant = 1 , product.vendor_code, v_product.vendor_code) as vendor_code',
                    'IF(product.is_variant = 1 , product.amount, v_product.amount) as amount'
                ])
                ->leftJoin('v_product', 'product.id = v_product.product_id')
                ->leftJoin('category', 'product.category_id = category.id')
                ->where(['not', ['product.publish_status' => NULL]])
                ->asArray()
                ->orderBy(['product.category_id' => SORT_ASC, 'product.id' => SORT_DESC])
                ->all();
        return $product;
    }

    /**
     * @SWG\Get(path="/api/getVProducts",
     *     tags={"Product"},
     *     summary="Получение всех вариативных товаров",
     *     @SWG\Response(
     *         response = 200,
     *         description = "Ok",
     *         @SWG\Schema(ref = "#/")
     *     ),
     *     @SWG\Response(
     *         response = 400,
     *         description = "Bad Request",
     *         @SWG\Schema(ref = "#/")
     *     ),
     *     @SWG\Response(
     *         response = 404,
     *         description = "Not Found",
     *         @SWG\Schema(ref = "#/")
     *     ),
     *     @SWG\Response(
     *         response = 500,
     *         description = "Internal Server Error"
     *     )
     * )
     */
    public function actionGetVProducts() {
        $vproduct = VProduct::find()
                ->select([
                    'v_product.id', 'v_product.product_id', 'product.name AS product_name', 'product.category_id', 'category.name AS category_name',
                    'v_product.char_value', 'v_product.amount', 'v_product.price1', 'v_product.price2'
                ])
                ->leftJoin('product', 'product.id = v_product.product_id')
                ->leftJoin('category', 'product.category_id = category.id')
                ->where(['not', ['product.publish_status' => NULL]]) // продукт разрешен для публикации в магазине
                ->andWhere(['is_variant' => 2]) // вариативный продукт
                ->asArray()
                ->orderBy(['product.category_id' => SORT_ASC])
                ->all();
        $characteristic_value = ArrayHelper::index(CharacteristicValue::find()->select(['id', 'id_char', 'name'])->with('idChar')->asArray()->all(), 'id');
        $data = [];
        foreach ($vproduct as $k => $v) {
            $vproduct[$k]['char_value'] = unserialize($vproduct[$k]['char_value']);
            $temp = [];
            foreach ($vproduct[$k]['char_value'] as $k1 => $v1) {
                $temp[$characteristic_value[$v1]['idChar']['name']] = $characteristic_value[$v1]['name'];
            }
            $vproduct[$k]['char_value'] = implode(' | ', $temp);
            $data[$v['category_id']][$v['product_id']]['product_id'] = $v['product_id'];
            $data[$v['category_id']][$v['product_id']]['product_name'] = $v['product_name'];
            $data[$v['category_id']][$v['product_id']]['category_id'] = $v['category_id'];
            $data[$v['category_id']][$v['product_id']]['category_name'] = $v['category_name'];
            $data[$v['category_id']][$v['product_id']]['items'][] = [
                'id' => $v['id'],
                'char_value' => implode(' | ', $temp),
                'attributes' => $temp,
                'amount' => $v['amount']
            ];
        }
        return $data;
    }

}
