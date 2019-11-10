<?php
namespace app\console\models;

use app\console\models\BaseSyncModel;
use app\models\Product;
use app\models\Operations;
use app\models\OperComing;
use app\models\OperConsumption;
use app\controllers\OperationMassConsumptionController;
use app\controllers\OperationConsumptionController;
use app\controllers\OperationComingController;
use app\controllers\LiveEditController;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;


class TransactionProduct extends BaseSyncModel
{
    private $action;
    private $needModel;
    private $newModel;
    private $requestData;
    private $title;
    private $continueKey = [
        'id',
        'transaction',
        'old_value',
        'prod_value',
        'created_at',
        'change_price',
        'updated_at',
        'date_update'
    ];
    //массив ключей для проверки 'typeData' => 'keyField'
    private $arrayCheckFieldKey = [
        'product' => [
            'amount' => 'int',
            'start_price' => 'double',
            'trade_price' => 'double',
            'cost_price' => 'double',
            'price' => 'double',
            'price1' => 'double',
            'price2' => 'double',
        ],
        'transaction' => [
            'total_usd' => 'double',
            'total_ua' => 'double',
            'course' => 'course',
            'start_price' => 'double',
            'trade_price' => 'double',
            'cost_price' => 'double'   
        ]
    ];
    //не соответствующие ключи (старая => новая)ProductRequest::FormatProdValueProdRequest Operations::getArrayForJSONs
    private $brokenKey = [
        'id_category' => 'category_id',
        'id_agent' => 'agent_id'
    ];

    private $keySliceTransaction = [
        'total_usd',
        'trade_price',
        'cost_price'
    ];

    private $keySliceProduct = [
        'cost_price',
        'trade_price'
    ];

    public function __construct(array $data) 
    {
        $this->action = $data['action'];
        
        if(isset($data['data']['transaction']['type']) || isset($data['data']['transaction'][0]['type'])){
            $this->type = $data['data']['transaction']['type'] ?? $data['data']['transaction'][0]['type'];
        }
        
        $this->needModel = $data['data'];
        
        if(isset($data['data']['transaction'])){
            $this->needModel['transaction'] = $data['data']['transaction'];

        }
        
        if(isset($data['data']['product'])){

            $this->needModel['product'] = $data['data']['product'];
        }
        
        $this->requestData = $data['requestData']['body'];

        if($data['requestData']['title'] != 'add-product-mass-transaction' && $data['requestData']['title'] != 'delete-product-mass-transaction' && $data['requestData']['title'] != 'delete-mass-transaction-product'){
            $this->requestData['transaction_id'] = $this->needModel['transaction']['id'];
            $this->requestData['product_id'] = $this->needModel['product']['id'];
        }else{
            $this->title = $data['requestData']['title'];
        }
    }

    public function entry()
    {
        switch(true){
            case !empty($this->title) && $this->title === 'add-product-mass-transaction' : 
                return $this->createMass();
            break;

            case $this->action === 'create' : 
                return $this->create();
            break;
            
            case $this->action === 'update' : 
                return $this->update();
            break;

            case $this->title === 'delete-product-mass-transaction': 
                return $this->deleteFromMass();
            break; 

            case $this->title === 'delete-mass-transaction-product' :
                return $this->deleteMass();
            break;

            case $this->action === 'delete' : 
                return $this->delete();
            break;
        }
    }
    
    private function create()
    {
        if($this->type == 1){
            $this->newModel = new OperComing();
            $this->newModel->load(['OperComing' => $this->requestData]);
        }else{
            $this->newModel = new OperConsumption();
            $this->newModel->load(['OperConsumption' => $this->requestData]);
        }

        if($this->newModel->save()){
            $this->clearData();
        }else{
            $error = implode('|',$this->newModel->getErrorSummary(true));

            Curl::sendMsgTelegram("product {$this->needModel['product']['id']} , {$error}" , 'alert');

            return false;
        }
    }
    
    private function createMass()
    {
        foreach($this->requestData as $key => $one){
            $this->newModel[$key] = new OperConsumption();

            $error[] = $this->newModel[$key]->SaveVariant(
                [
                    'product_id' => null,
                    'amount' => $one['amount'],
                    'price1' => $one['price']
                ],
                [
                    'product_id' => substr($one['product_idt'],1),
                    'transaction_id' => $this->needModel['transaction'][$key]['id']
                ]
            );

            if(!is_null($error[0])){
                $id = $this->needModel['product'][$key]['id'];

                Curl::sendMsgTelegram("create mass transaction {$id} , " . implode('|',$error[$key]) , 'alert');
    
                return false;
            }
        }
        $this->clearDataMass(['product' => true, 'transaction' => false]);
    }

    private function update()
    {
        if($this->requestData['field'] == 'amount-consumption'){
            $this->requestData['field'] = 'amount';
        } 

        $data = [
            'typeLifeEdit' => $this->needModel['transaction']['type'] == 1 ? 'edit-coming' : 'edit-consumption',
            'field' => $this->requestData['field'],
            'value' => $this->requestData['value'],
            'productId' => $this->requestData['id'],
            'variantId' => '',
            'transaction_id' => $this->needModel['transaction']['id'],
            'variant' => false
        ];
        
        $action = Json::decode(LiveEditController::sync($data));
        
        if($action['status'] == true){
            $this->clearData(['product' => true,'transaction' => false]);
        }else {
            Curl::sendMsgTelegram("live edit transaction {$this->needModel['transaction']['id']} , {$action['text']}" , 'alert');

            return false;
        }
    }
    
    private function delete()
    {
        $transaction = Operations::findOne(['id' => $this->requestData['transaction_id']]);
        
        if($this->requestData['type'] == 'consumption'){
            OperationConsumptionController::actionDeleteProduct([
                'transaction' => $transaction->id,
                'base' => $this->requestData['product_id'],
                'variant' => null
            ]);
        }else{
            OperationComingController::actionDeleteProduct([
                'transaction' => $transaction->id,
                'base' => $this->requestData['product_id'],
                'variant' => null
            ]);
        }

        $this->clearData();
    }

    //cancel
    public function deleteMass()
    {
        $transactionId = $this->requestData;
        $product = ArrayHelper::index($this->needModel['product'],'id');
        
        foreach($transactionId as $one){
            $transaction = Operations::findOne(['id' => $one]);
            
            foreach ($transaction->getProducts()->all() as $oneProduct) {
                OperationMassConsumptionController::actionDeleteProduct([
                    'transaction' => $transaction->id,
                    'base' => $oneProduct->product_id,
                    'variant' => null
                ]);

                if(!isset($product[$oneProduct->product_id])){
                    Curl::sendMsgTelegram(
                        "mass transaction {$transaction->id} product {$oneProduct->product_id} , lost in data array", 
                        'alert'
                    );
                }
            }
        }
    }

    private function deleteFromMass()
    {
        $product = $this->needModel['product'];
        $transactionId = str_replace(['[',']','"'],'',$this->requestData['trans']);
        $arrayTransactionId = explode(',',$transactionId);

        
        foreach($arrayTransactionId as $one){
            OperationMassConsumptionController::actionDeleteProduct([
                'transaction' => $one,
                'base' => $product['id'],
                'variant' => null
            ]);
        }
    }

    private function clearData(array $target = ['product' => true, 'transaction' => true])
    {
        if($target['product']){
            $product = Product::find()->asArray()->where(['id' => $this->needModel['product']['id']])->one();
            $this->checkProduct($product, $this->needModel['product'], $this->needModel['transaction']['id']);
        }
        
        if($target['transaction']){
            $transaction = \app\models\Operations::find()->asArray()->where(['id' => $this->needModel['transaction']['id']])->one();
            $this->checkTransaction($transaction, $this->needModel['transaction']);
        }
    } 
    
    private function clearDataMass(array $target = ['product' => true, 'transaction' => true])
    {
        if($target['transaction']){
            foreach($this->needModel['transaction'] as $oneTransaction){
                $transaction = \app\models\Operations::find()->asArray()->where(['id' => $oneTransaction['id']])->one();
                $this->checkTransaction($transaction, $oneTransaction);
            }
        }
        
        if($target['product']){
            $needProduct = end($this->needModel['product']);
            $product = Product::find()->asArray()->where(['id' => $needProduct['id']])->one();
            $this->checkProduct($product, $needProduct, end($this->needModel['product'])['id']);
        }
    }

    private function checkProduct($product,$needProduct,$transactionId)
    {
        foreach($needProduct as $key => $one){
            if(in_array($key,$this->continueKey)){ 
                continue;
            }
            if($key == 'id_category' || $key == 'id_agent'){
                $key = $this->brokenKey[$key];
            }
            if(in_array($key, $this->keySliceProduct)){
                $product[$key] = $this->truncate_number($product[$key],\Yii::$app->params['float']);
                $one = $this->truncate_number($one,\Yii::$app->params['float']);
            }
            if($product[$key] != $one){
                Curl::sendMsgTelegram(
                    "product {$needProduct['id']} , transaction {$transactionId}, new => $product[$key], old => $one , key => $key", 

                    $this->getTypeError($product[$key],$one,$this->arrayCheckFieldKey['product'][$key] ?? '')

                );
            }
        }  
    }
    
    private function checkTransaction($transaction,$needTransaction)
    {
        foreach($needTransaction as $key => $one){
            if(in_array($key,$this->continueKey)){ 
                continue;
            }
            if(in_array($key, $this->keySliceTransaction)){
                $transaction[$key] = $this->truncate_number($transaction[$key],\Yii::$app->params['float']);
                $one = $this->truncate_number($one,\Yii::$app->params['float']);
            }
            if($transaction[$key] != $one){
                Curl::sendMsgTelegram(
                    "transaction {$needTransaction['id']} , new => $transaction[$key] , old => $one , key => $key", 

                    $this->getTypeError($transaction[$key],$one,$this->arrayCheckFieldKey['transaction'][$key] ?? '')

                );
            }
        }
    }
}
