<?php

namespace app\modules\manager\services;

use app\modules\manager\models\Request;
use app\modules\manager\type\ProductPrice;
use app\modules\manager\models\RequestProduct;

class RequestService
{
    private $transaction_service;

    public function __construct(TransactionService $transaction)
    {
        $this->transaction_service = $transaction;
    }

    //сохраняет продукт для заявки
    public function saveRequest($post,ProductPrice $price)
    {
        $model = new RequestProduct ();
        $model->request_id = (int)$post['request_id'];
        $model->product_id = (int)$post['id'];
        $model->amount = (int)$post['amount'];
        $model->price = (float)$price->price;
        $model->cost_price = (float)$price->cost_price;
        $model->trade_price = (float)$price->trade_price;
        if(!$model->save()){
            throw new \DomainException('Saving error.');
        }
    }

    //сохраняет вариативный продукт для заявки
    public function saveRequestV($post)
    {
        \Yii::$app->db->createCommand()->batchInsert(
            'request_product',['request_id','product_id','vproduct_id','amount','price','cost_price','trade_price'],
            array_map(function($item) use ($post) {
                return [
                    'request_id' => $post['request_id'],
                    'product_id' => $post['id'],
                    'vproduct_id' => $item[0],
                    'amount' => $item[1],
                    'price' => (float)$item[2],
                    'cost_price' => (float)$post['cost_price'],
                    'trade_price' => (float)$post['trade_price'],
                ];
            },$post['vprod_data'])
        )->execute();
    }

    //меняет кол-во
    public function changeAmount($post)
    {
        $product = $this->getProduct($post['request_id'],$post['prod_id'],isset($post['vprod_id'])?$post['vprod_id']:null);

        $product->amount = (int)$post['amount'];
        $product->update();
    }

    //потверждения заявки
    public function confirmRequest($post)
    {
        $request = Request::findOne($post['request_id']);
        $request->comment = $post['comment'];
        $request->status = Request::REQUEST_ACTIVE;
        $request->updated_at = time();
        $request->update();
    }

    //удаляет продукт из заявки
    public function removeProduct($post)
    {
        $product = $this->getProduct($post['request_id'],$post['prod_id'],$post['vprod_id']);
        $product->delete();
        $this->transaction_service->isProductForRequest($post['request_id']);
    }

    //удаляет все продукты из заявки
    public function clearRequest($request_id)
    {
        if(RequestProduct::deleteAll(['request_id' => (int)$request_id])) {

            $this->transaction_service->isProductForRequest($request_id);
            RequestProduct::deleteAll(['request_id' => (int)$request_id]);
        } else {
           throw new \DomainException('Ошибка удаления');
        }
    }

    private function getProduct($request_id,$product_id,$vproduct_id=null)
    {
        if($vproduct_id){
            return RequestProduct::find()
                ->where(['request_id' => $request_id])
                ->andWhere(['product_id' => $product_id])
                ->andWhere(['vproduct_id' => $vproduct_id])
                ->one();
        }
        return RequestProduct::find()
            ->where(['request_id' => $request_id])
            ->andWhere(['product_id' => $product_id])->one();
    }
}