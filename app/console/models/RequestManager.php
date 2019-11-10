<?php
namespace app\console\models;

use app\console\models\BaseSyncModel;
use app\models\Operations;
use app\models\OperConsumption;
use app\modules\manager\models\Request;
use app\modules\manager\models\RequestProduct;
use app\models\Agent;

date_default_timezone_set('Europe/Kiev');

class RequestManager extends BaseSyncModel
{ 
    private $action;
    private $needModel;
    private $requestProduct;
    private $requestData;
    private $request;
    private $transaction;
    //массив ключей для проверки 'typeData' => 'keyField'
    private $arrayCheckFieldKey = [
        'transaction' => 'transaction',
        'status' => 'status',
        'total_usd' => 'double',
        'total_ua' => 'double',
        'course' => 'course',
        'trade_price' => 'double',
        'cost_price' => 'double'
    ];

    public function __construct(array $data)
    {
        $this->action = $data['action'];
        $this->needModel = $data['data'];
        $this->requestData = $data['requestData'];

        if(isset($this->requestData['body']['store_id'])){
            $requestStore = Request::find()->where(['store_id' => $this->requestData['body']['store_id']])->one();
            $this->request = $requestStore;
        }
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
                return $this->cleaning();
            break;
        }
    }

    private function create()
    {
        if($this->requestData['title'] == 'create-empty-transaction'){
            $transaction = new Operations();
            $transaction->id = $this->needModel['id'];
            $transaction->whence = $this->needModel['whence'];
            $transaction->where = $this->needModel['where'];
            $transaction->status = Operations::OPERATION_EMPTY;
            $transaction->type = $this->needModel['type'];
            $transaction->date = $this->needModel['date'];
            $transaction->course = $this->needModel['course'];

            if(!$transaction->save()){
                Curl::sendMsgTelegram("transaction {$this->needModel['transaction']} , " . implode(' | ',$transaction->getErrorSummary(true)) , 'alert');

                return false;
            }
        }
    }
    //создания транзакций с заявки менеджера и потверждения заявки
    private function update()
    {
        if($this->requestData['title'] == 'create_transaction_from_request'){

            $price = 0;
            $tradePrice = 0;
            $costPrice = 0;

            foreach ($this->requestData['body']['arr_product'] as $productId){
                $productRequest = RequestProduct::find()->where(['request_id' => $this->request->id])
                    ->andWhere(['product_id' => $this->sliceId($productId)])->one();

                if(empty($productRequest)){
                    Curl::sendMsgTelegram("product {$productId} request id : {$this->request->id}, not found" , 'alert');

                    return false;
                }
                $productAmount = $productRequest->getProduct()->asArray()->one();
                
                if ($productAmount['amount'] >= $productRequest->amount) {
                    $amount = $productRequest->amount;
                    $delete = true;
                } else {
                    $amount = $productAmount['amount'];
                    $delete = false;
                }
                
                $operConsumption = new OperConsumption();
                $operConsumption->transaction_id = $this->requestData['body']['transaction_id'];
                $operConsumption->product_id = $this->sliceId($productId);
                $operConsumption->amount = $amount;
                $operConsumption->price = $productRequest->price;
                $price += ($operConsumption->price * $operConsumption->amount);
                $operConsumption->trade_price = $productRequest->trade_price;
                $tradePrice += ($operConsumption->trade_price * $operConsumption->amount);
                $operConsumption->cost_price = $productRequest->cost_price;
                $costPrice += ($operConsumption->cost_price * $operConsumption->amount);

                if(!$operConsumption->save()){

                    Curl::sendMsgTelegram("product {$this->sliceId($productId)} , transaction {$this->requestData['body']['transaction_id']}" . implode(' | ',$operConsumption->getErrorSummary(true)) , 'alert');


                    return false;
                }
                if ($delete) {
                    $productRequest->delete();
                } else {
                    $productRequest->amount = $productRequest->amount - $productAmount['amount'];
                    $productRequest->update();
                }
            }

            $this->transaction = Operations::find()->where(['id' => $this->requestData['body']['transaction_id']])->one();
            $this->transaction->trade_price = $tradePrice;
            $this->transaction->cost_price = $costPrice;
            $this->transaction->total_ua = $price;
            $this->transaction->status = Operations::OPERATION_FULL;
            $this->transaction->transaction = $this->needModel['transaction']['transaction'];
            $this->transaction->course = $this->needModel['transaction']['course'];
            $this->transaction->total_usd = $this->transaction->total_ua/$this->needModel['transaction']['course'];
            if(!$this->transaction->update()){
                Curl::sendMsgTelegram("transaction {{$this->requestData['body']['transaction_id']} , по request {$this->request->id} " . implode(' | ',$this->transaction->getErrorSummary(true)) , 'alert');

                return false;
            }

            $this->request->status = Request::REQUEST_NOT_EMPTY;
            $this->request->comment = $this->requestData['body']['comment'];
            $this->request->updated_at = time();
            if(!$this->request->save()){
                Curl::sendMsgTelegram("request {$this->request->id} " . implode(' | ',$this->request->getErrorSummary(true)) , 'alert');

                return false;
            }

            $this->clearData();
        } else {

            $this->request->comment = $this->requestData['body']['comment'];
            $this->request->status = Request::REQUEST_ACTIVE;
            $this->request->updated_at = time();

            if(!$this->request->save()){
                Curl::sendMsgTelegram("request {$this->request->id} " . implode(' | ',$this->request->getErrorSummary(true)) , 'alert');

                return false;
            }
        }
    }
    
    private function cleaning()
    {
        $conditional = ['request_id' => $this->needModel['id']];

        RequestProduct::deleteAll($conditional);

        $countRow = RequestProduct::find()->where($conditional)->count();

        if($countRow > 0){
            Curl::sendMsgTelegram('Товары заявки id : ' . $this->needModel['id'] . ' удалены не всё . Текущие колличество : ' . $countRow , 'alert');
        }else{
            Request::updateAll(['status' => Request::REQUEST_INACTIVE], ['id' => $this->needModel['id']]);
        }
    }

    private function sliceId($id)
    {
        return substr($id,  1);
    }

    private function clearData()
    {
        foreach($this->arrayCheckFieldKey as $oneKey => $typeData){
            $old = $this->needModel['transaction'][$oneKey];
            $new = $this->transaction->{$oneKey};

            if($old != $new){
                Curl::sendMsgTelegram(
                    "transaction-request-product {$this->transaction->id} , new => {$new} , old => {$old} , key => $oneKey" , 
                    $this->getTypeError($new,$old,$typeData)
                );
            }
        }
    }
}
