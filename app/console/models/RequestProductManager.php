<?php

namespace app\console\models;

use app\console\models\BaseSyncModel;
use app\modules\manager\models\Request;
use app\modules\manager\models\RequestProduct;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class RequestProductManager extends BaseSyncModel
{
    private $action;
    private $needModel;
    private $requestProduct;
    private $requestData;
    private $request;
    //массив ключей для проверки 'typeData' => 'keyField' если ключ равен полю типизаций не будет
    private $arrayCheckFieldKey = [
        'amount' => 'int',
        'price' => 'double',
        'trade_price' => 'double',
        'cost_price' => 'double',
    ];

    public function __construct(array $data)
    {

        $this->action = $data['action'];
        $this->needModel = $data['data'];
        $this->requestData = $data['requestData'];

        $requestStore = Request::find()->where(['store_id' => $this->requestData['body']['store_id']])->one();

        if(empty($requestStore)){
            $requestStore = $this->createRequest($this->requestData['body']['store_id']);

            Curl::sendMsgTelegram("request shop {$this->requestData['body']['store_id']} , not found but create" , 'alert');
        }

        $this->request = $requestStore;
    }

    public function entry()
    {
        switch(true){
            case $this->action === 'create' :
                return $this->create();
            break;

            case $this->action === 'update' :
                return $this->update();
            break;

            case $this->action === 'delete' :
                return $this->delete();
            break;
        }
    }

    private function create()
    {
        $this->requestProduct = new RequestProduct();
        $this->requestProduct->request_id = $this->request->id;
        $this->requestProduct->product_id = $this->requestData['body']['id'];
        $this->requestProduct->amount = $this->requestData['body']['amount'];
        $this->requestProduct->price = $this->requestData['product']['price'];
        $this->requestProduct->cost_price = $this->requestData['product']['cost_price'];
        $this->requestProduct->trade_price = $this->requestData['product']['trade_price'];

        if($this->requestProduct->save()){
            $this->clearData();
        }else{
            Curl::sendMsgTelegram("request-product {$this->needModel['id']} , " . implode(' | ',$this->requestProduct->getErrorSummary(true)) , 'alert');
            
            return false;
        }
    }

    private function update()
    {
        $this->requestProduct = RequestProduct::find()
            ->where(['request_id' => $this->request->id])
            ->andWhere(['product_id' => $this->requestData['body']['prod_id']])
            ->one();

        if(empty($this->requestProduct)){
            Curl::sendMsgTelegram("request-product {$this->requestData['body']['prod_id']} , not found " , 'alert');

            return false;
        }

        $this->requestProduct->amount = $this->requestData['body']['amount'];

        if($this->requestProduct->save()){
            $this->clearData();
        }else{
            Curl::sendMsgTelegram("request-product {$this->needModel['id']} , " . implode(' | ',$this->request->getErrorSummary(true)) , 'alert');

            return false;
        }
    }

    public function delete()
    {
        $this->requestProduct = RequestProduct::find()
            ->where(['request_id' => $this->request->id])
            ->andWhere(['product_id' => $this->requestData['body']['prod_id']])
            ->one();

        if(empty($this->requestProduct)){
            Curl::sendMsgTelegram("request-product {$this->requestData['body']['prod_id']} , not found " , 'alert');

            return false;
        }

        if($this->requestProduct->delete()){
            $check = RequestProduct::find()
                ->where(['request_id' => $this->request->id])
                ->andWhere(['product_id' => $this->requestData['body']['prod_id']])
                ->exists();

            if($check){
                Curl::sendMsgTelegram("request-product {$this->requestData['body']['prod_id']} ,  is not delete");
            }
        }else{
            Curl::sendMsgTelegram("request-product {$this->needModel['id']} , " . implode(' | ',$this->request->getErrorSummary(true)) , 'alert');

            return false;
        }
    }

    private function createRequest($storeId)
    {
        $request = new Request();
        $request->store_id = $storeId;
        $request->status = Request::REQUEST_INACTIVE;
        $request->created_at = time();
        $request->updated_at = time();
        $request->save();

        return $request;
    }

    private function getArrayForJSONs($json)
    {
        $arr_value = explode('}', $json);
        $arr = array_slice($arr_value, 0, -1);
        $new_arr = [];

        foreach ($arr as $one) {

            $string = substr_replace($one, '}', strlen($one), 0);
            $new_arr[] = JSON::decode($string);
        }

        return $new_arr;
    }

    public function clearData()
    {
        $product = ArrayHelper::index($this->getArrayForJSONs($this->needModel['prod_value']), 'product');
        $needProduct = $product['p' . $this->requestProduct->product_id];
        
        foreach($this->arrayCheckFieldKey as $oneKey => $typeData){
            $old = $needProduct[$oneKey];
            $new = $this->requestProduct->{$oneKey};

            if($old != $new){
                Curl::sendMsgTelegram(
                    "request-product {$this->requestProduct->id} request ". $this->needModel['id'] .", new => {$new} , old => {$old} , key => $oneKey",
                    $this->getTypeError($new,$old,$typeData)
                );
            }
        }
    }
}
