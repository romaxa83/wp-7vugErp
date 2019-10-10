<?php

namespace app\modules\manager\services;


use app\models\Operations;
use app\models\Product;
use app\models\VProduct;
use app\modules\manager\models\Request;
use app\modules\manager\models\RequestProduct;
use yii\helpers\ArrayHelper;
use app\service\BazaApi;

class TransactionService
{
    //формирует пустую транзакцию
    public function createTransaction($post)
    {
        $transaction = new Operations();
        $transaction->course = \Yii::$app->session->get('getSettingSession')['usd'];
        $transaction->whence = 1;
        $transaction->where = $post['store_id'];
        $transaction->type = 2;
        $transaction->status = Operations::OPERATION_EMPTY;
        $transaction->date = date('Y-m-d H:i:s');
        $transaction->date_update = date('Y-m-d H:i:s');
        if(!$transaction->save()){
            throw new \DomainException('Transaction saving error.');
        }
        $transaction->transaction = $transaction->id . '00' . rand(100, 999);
        $transaction->update();
        return $transaction->id;
    }

    //заполняет транзакцию товарами
    public function fillTransaction($post)
    {
        $products = $this->getProducts($post['arr_product'],$post['request_id']);

        if(!empty($this->getVProductId($post['arr_product']))){
            $vproducts = $this->getVProductsOut($post['arr_product'],$post['request_id']);
            $products = $this->diffProduct($products,$vproducts);
        }
        
        $transaction = \Yii::$app->db->beginTransaction();
        
        try {
            $result = $this->fillOperConsumption($products,$post['transaction_id']);
            if(count($result['temp']['update']) > 0  || count($result['temp']['delete']) > 0) {
                $successProduct = array_diff_key(ArrayHelper::index($products,'product_id'), $result['error']);
                $trans = Operations::findOne($post['transaction_id']);
                $trans->saveTotalValue();
                $trans->status = Operations::OPERATION_FULL;
                $trans->date_update = date('Y-m-d H:i:s');
                $trans->update();
                
                $this->removeRequestProduct(array_keys($result['temp']['delete']));
                $this->updateRequestProduct($result['temp']['update']);
                $this->isProductForRequest($post['request_id']);
                
                /*****___SEND_TO_API___ ******/
                $dataApi['requestData']['title'] = BazaApi::REQUEST_TITLE_CREATE_TRANSACTION;
                $dataApi['requestData']['body'] = $post;
                $dataApi['data']['transaction'] = $trans->getAttributes();
                $dataApi['data']['product'] = ArrayHelper::getColumn($successProduct,'product');
                (new BazaApi('request','update'))->add($dataApi);
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
        return ['error' => $result['error'],'transaction' => $trans->transaction ?? ''];
    }


    //снимает активный статус заявки ,если в ней нет товаров
    public function isProductForRequest($request_id)
    {
        if(!(RequestProduct::find()->where(['request_id' => $request_id])->exists())){
            $request = Request::findOne($request_id);
            $request->comment = null;
            $request->status = Request::REQUEST_INACTIVE;
            $request->updated_at = time();
            $request->update();
        }
    }

    //возвращает массив выбраных продуктов
    private function getProducts($array_ids,$request_id)
    {
        return ArrayHelper::index(RequestProduct::find()->with('product')->where(['request_id' => $request_id])
            ->andWhere(['in','product_id',$this->getProductId($array_ids)])
            ->asArray()
            ->all(),'id');
    }

    //возвращает массив невыбраных вариативных продуктов
    private function getVProductsOut($array_ids,$request_id)
    {
        return ArrayHelper::index(RequestProduct::find()->where(['request_id' => $request_id])
            ->andWhere(['not in','vproduct_id',$this->getVProductId($array_ids)])
            ->asArray()
            ->all(),'id');
    }

    //возвращает продукты без невыбраных вариативных товаров
    private function diffProduct($products_array,$vproducts_array)
    {
        return array_map('unserialize',array_diff(array_map('serialize',$products_array),array_map('serialize',$vproducts_array)));
    }

    //заполняет выбраными продуктами таблицу oper_consumption
    private function fillOperConsumption($products,$transaction_id)
    {        
        $result = [];
        $temp['update'] = [];
        $temp['delete'] = [];
        foreach ($products as $item){
            $model = new \app\models\OperConsumption();
            if($item['product']['amount'] >= $item['amount']){
                $amount = $item['amount'];
                $temp['delete'][$item['id']] = true;
            } else {
                $amount = $item['product']['amount'];
                $temp['update'][$item['id']] = $item['amount'] - $item['product']['amount'];
            }
            $model->load(['OperConsumption' => [
                'transaction_id' => $transaction_id,
                'product_id' => $item['product_id'],
                'vproduct_id' => $item['vproduct_id'],
                'amount' => $amount,
                'price' => (float)$item['price'],
                'cost_price' => (float)$item['cost_price'],
                'trade_price' => (float)$item['trade_price'],
            ]]);
            if(!$model->save()){
                $result[$item['product_id']]['error'] = $model->getErrorSummary(true);
                $result[$item['product_id']]['name'] = $item['product']['name'];
            }
        }
        return ['error' => $result, 'temp' => $temp];
    }

    //удаляет товары из заявки
    private function removeRequestProduct($ids)
    {
        RequestProduct::deleteAll(['id' => $ids]);
    }
    
    //редактирует кол-во товаров из заявки
    private function updateRequestProduct($product)
    {
        foreach ($product as $key => $item) {
            $rp = RequestProduct::findOne($key);
            $rp->amount = $item;
            $rp->update();
        }
    }

    //отнимает товар
    private function changeAmountProducts($products)
    {
        foreach($products as $one){
            if(!empty($one['vproduct_id']) && $one['vproduct_id'] !== null){
                $vproduct = VProduct::findOne($one['vproduct_id']);
                $vproduct->amount = $vproduct->amount - $one['amount'];
                $vproduct->update();
            }
            if($one['product']['amount'] >= $one['amount']){
                $product = Product::findOne($one['product_id']);
                $product->amount = $product->amount - $one['amount'];
                $product->update();
            }
        }
    }

    //возвращает массив id-продуктов
    private function getProductId($array)
    {
        return array_map(function($item){
            return $item[0];
        },$array);
    }

    //возвращает массив id-вариативных продуктов
    private function getVProductId($array)
    {
        return array_values(array_diff(array_map(function($item){
            return $item[1];
        },$array),array('')));
    }

}