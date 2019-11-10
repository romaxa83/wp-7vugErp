<?php

namespace app\controllers\api;

use Yii;
use yii\rest\Controller;
use app\modules\order\models\Order;
use app\modules\order\models\OrderProduct;
use app\models\Curl;
use yii\helpers\Json;

class OrderController extends Controller {

    /**
     * @SWG\Put(path="/api/createOrder",
     *     tags={"Order"},
     *     summary="Создание заказа",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="order_id", type="integer")
     *         )
     *     ),
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
    public function actionCreateOrder() {
        $post = Yii::$app->request->post();
        $curl_body = Curl::curl('POST', '/admin/api/getOrder', ['order_id' => $post['order_id']]);
        if ($curl_body['body'] != FALSE) {
            $order = Order::find()->asArray()->where(['order' => $post['order_id']])->one();
            if ($order !== NULL) {
                Yii::$app->response->statusCode = 400;
                return [
                    'status' => '400',
                    'message' => 'Заказ уже создан'
                ];
            }
            $order = new Order();
            $order->order = $curl_body['body']['id'];
            $order->date = $curl_body['body']['date'];
            $order->amount = $curl_body['body']['sum'];
            $order->status = $curl_body['body']['status'];
            $order->save();
            return [
                'status' => '200',
                'message' => 'Заказ успешно создан'
            ];
        }
        Yii::$app->response->statusCode = 400;
        return [
            'status' => '400',
            'message' => 'Заказ не создан'
        ];
    }

    /**
     * @SWG\Put(path="/api/updateOrder",
     *     tags={"Order"},
     *     summary="Редактирование заказа",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="order_id", type="integer")
     *         )
     *     ),
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
    public function actionUpdateOrder() {
        $post = Yii::$app->request->post();
        $curl_body = Curl::curl('POST', '/admin/api/getOrder', ['order_id' => $post['order_id']]);
        if ($curl_body['body'] != FALSE) {
            $order = Order::find()->where(['order' => $post['order_id']])->one();
            if ($order === NULL) {
                Yii::$app->response->statusCode = 400;
                return [
                    'status' => '400',
                    'message' => 'Заказ не найден'
                ];
            }
            $order->order = $curl_body['body']['id'];
            $order->date = $curl_body['body']['date'];
            $order->amount = $curl_body['body']['sum'];
            $order->status = $curl_body['body']['status'];
            $order->save();
            return [
                'status' => '200',
                'message' => 'Заказ успешно отредактирован'
            ];
        }
        Yii::$app->response->statusCode = 400;
        return [
            'status' => '400',
            'message' => 'Заказ не найден'
        ];
    }

    /**
     * @SWG\Put(path="/api/deleteOrder",
     *     tags={"Order"},
     *     summary="Удаление заказа",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="order_id", type="integer")
     *         )
     *     ),
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
    public function actionDeleteOrder() {
        $post = Yii::$app->request->post();
        $curl_body = Curl::curl('POST', '/admin/api/getOrder', ['order_id' => $post['order_id']]);
        if ($curl_body['body'] != FALSE) {
            $order = Order::deleteAll(['order' => $post['order_id']]);
            if ($order != 0) {
                return [
                    'status' => '200',
                    'message' => 'Заказ успешно удален'
                ];
            }
        }
        Yii::$app->response->statusCode = 404;
        return [
            'status' => '404',
            'message' => 'Заказ не найден'
        ];
    }

    /**
     * @SWG\Put(path="/api/createOrderProducts",
     *     tags={"Order"},
     *     summary="Создание продуктов в заказ",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="order_id", type="integer")
     *         )
     *     ),
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
    public function actionCreateOrderProducts() {
        $order_id = [];
        $post = Yii::$app->request->post();
        $curl_body = Curl::curl('POST', '/admin/api/getOrder', ['order_id' => $post['order_id']]);
        if ($curl_body['body'] != FALSE) {
            foreach ($curl_body['body']['orderProduct'] as $product) {
                $order_product = OrderProduct::find()->asArray()->where(['order_id' => $post['order_id'], 'product_id' => $product['product_id'], 'vproduct_id' => $product['vproduct_id']])->one();
                if ($order_product !== NULL) {
                    continue;
                }
                $order_product = new OrderProduct();
                $order_product->order_id = $curl_body['body']['id'];
                $order_product->product_id = $product['product_id'];
                $order_product->vproduct_id = $product['vproduct_id'];
                $order_product->amount = $product['count'];
                $order_product->price = 0;
                $order_product->confirm = 0;
                $order_product->save();
                $order_id[] = $order_product->product_id;
            }
        }
        if (count($order_id) > 0) {
            return [
                'status' => '200',
                'message' => 'Продукты успешно созданы: ' . implode(', ', $order_id)
            ];
        } else {
            Yii::$app->response->statusCode = 400;
            return [
                'status' => '400',
                'message' => 'Продукты не созданы'
            ];
        }
    }

    /**
     * @SWG\Put(path="/api/createOrderProduct",
     *     tags={"Order"},
     *     summary="Создание продукта в заказ",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="order_id", type="integer"),
     *             @SWG\Property(property="product_id", type="integer"),
     *             @SWG\Property(property="vproduct_id", type="integer")
     *         )
     *     ),
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
    public function actionCreateOrderProduct() {
        $post = Yii::$app->request->post();
        $curl_body = Curl::curl('POST', '/admin/api/getOrderProduct', ['order_id' => $post['order_id'], 'product_id' => $post['product_id'], 'vproduct_id' => $post['vproduct_id']]);
        if ($curl_body['body'] != FALSE) {
            $order_product = OrderProduct::find()->where(['order_id' => $curl_body['body']['order_id'], 'product_id' => $curl_body['body']['product_id'], 'vproduct_id' => $curl_body['body']['vproduct_id']])->one();
            if ($order_product !== NULL) {
                Yii::$app->response->statusCode = 400;
                return [
                    'status' => '400',
                    'message' => 'Продукт уже создан'
                ];
            }
            $order_product->order_id = $curl_body['body']['order_id'];
            $order_product->product_id = $curl_body['body']['product_id'];
            $order_product->vproduct_id = $curl_body['body']['vproduct_id'];
            $order_product->amount = $curl_body['body']['count'];
            $order_product->price = 0;
            $order_product->confirm = 0;
            $order_product->save();
            return [
                'status' => '200',
                'message' => 'Продукт успешно отредактирован'
            ];
        }
        Yii::$app->response->statusCode = 404;
        return [
            'status' => '404',
            'message' => 'Продукт не найден'
        ];
    }

    /**
     * @SWG\Put(path="/api/updateOrderProduct",
     *     tags={"Order"},
     *     summary="Редактирование продукта в заказе",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="order_id", type="integer"),
     *             @SWG\Property(property="product_id", type="integer"),
     *             @SWG\Property(property="vproduct_id", type="integer")
     *         )
     *     ),
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
    public function actionUpdateOrderProduct() {
        $post = Yii::$app->request->post();
        $curl_body = Curl::curl('POST', '/admin/api/getOrderProduct', ['order_id' => $post['order_id'], 'product_id' => $post['product_id'], 'vproduct_id' => $post['vproduct_id']]);
        if ($curl_body['body'] != FALSE) {
            $order_product = OrderProduct::find()->where(['order_id' => $curl_body['body']['order_id'], 'product_id' => $curl_body['body']['product_id'], 'vproduct_id' => $curl_body['body']['vproduct_id']])->one();
            $order_product->amount = $curl_body['body']['count'];
            $order_product->price = 0;
            $order_product->confirm = 0;
            $order_product->save();
            return [
                'status' => '200',
                'message' => 'Продукт успешно отредактирован'
            ];
        }
        Yii::$app->response->statusCode = 404;
        return [
            'status' => '404',
            'message' => 'Продукт не найден'
        ];
    }

    /**
     * @SWG\Put(path="/api/deleteOrderProduct",
     *     tags={"Order"},
     *     summary="Удаление продукта с заказа",
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="order_id", type="integer"),
     *             @SWG\Property(property="product_id", type="integer"),
     *             @SWG\Property(property="vproduct_id", type="integer")
     *         )
     *     ),
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
    public function actionDeleteOrderProduct() {
        $post = Yii::$app->request->post();
        $curl_body = Curl::curl('POST', '/admin/api/getOrderProduct', ['order_id' => $post['order_id'], 'product_id' => $post['product_id'], 'vproduct_id' => $post['vproduct_id']]);
        if ($curl_body['body'] != FALSE) {
            $order_product = OrderProduct::deleteAll(['order_id' => $curl_body['body']['order_id'], 'product_id' => $curl_body['body']['product_id'], 'vproduct_id' => $curl_body['body']['vproduct_id']]);
            if ($order_product != 0) {
                return [
                    'status' => '200',
                    'message' => 'Продукт успешно удален'
                ];
            }
        }
        Yii::$app->response->statusCode = 404;
        return [
            'status' => '404',
            'message' => 'Продукт не найден'
        ];
    }

}
