<?php
namespace app\console\models;

use app\console\models\BaseSyncModel;
use app\models\Operations as BaseModel;
use app\models\Product;
use app\models\OperAdjustment;

class Transaction extends BaseSyncModel
{
    private $action;
    private $needModel;
    private $newModel;
    private $requestData;

    private $continueKey = [
        'id',
        'old_value',
        'prod_value',
        'date_update',
        'date'
    ];
    
    private $keySliceTransaction = [
        'total_usd',
        'trade_price',
        'cost_price'
    ];

    private $keyType = [
        'total_usd' => 'double',
        'total_ua' => 'double',
        'start_price' => 'double',
        'cost_price' => 'dobule',
        'trade_price' => 'double'
    ];


    public function __construct(array $data) 
    {
        $this->action = $data['action'];
        $this->needModel = $data['data'];
        $this->requestData = $data['requestData'];

        if(isset($data['requestData']['title']) && $data['requestData']['title'] != 'delete-mass-transaction'){
            $this->title = $data['requestData']['title'];

            if($this->action === 'delete'){
                $this->type = ($data['requestData']['body']['type'] === 'coming') ? 1 : 2;
            }else{
                $this->type = ($data['requestData']['title'] === 'coming') ? 1 : 2;
            }
        }
    }

    public function entry()
    {
        switch(true){
            case isset($this->title) && $this->title === 'adjustment-transaction' : 
                return $this->adjustment();
            break;

            case isset($this->requestData['title']) && $this->requestData['title'] === 'create-mass-transaction' :
                return $this->createMass();
            break;

            case $this->action === 'create' : 
                return $this->create();
            break;

            case isset($this->requestData['title']) && $this->requestData['title'] === 'stop-edit' : 
                return $this->stopEdit();
            break;

            case $this->action === 'update' : 
                return $this->update();
            break;

            case isset($this->requestData['title']) && $this->requestData['title'] ===  'delete-mass-transaction' :
                return $this->deleteMass();
            break;

            case $this->action === 'delete' : 
                return $this->delete();
            break;
        }
    }
    //создания приход расход(одиночный)
    private function create() 
    {
        $this->newModel = new BaseModel();
        $this->newModel->load(['Operations' => $this->requestData['body']]);
        $this->newModel->type = $this->type;

        if($this->newModel->save()){
            $this->newModel->transaction = $this->needModel['transaction'];
            $this->newModel->update();

            $this->clearData($this->needModel,$this->newModel);
        }else{
            Curl::sendMsgTelegram("create transaction {$this->needModel['id']} , " . implode(' | ',$this->newModel->getErrorSummary(true)) , 'alert');

            return false;
        }
    }
    //создания масс расхода
    private function createMass()
    {
        $dataModel = [];
        foreach ($this->requestData['body']['stores'] as $key => $one){
            $dataModel['whence'] = $this->requestData['body']['sclad'];
            $dataModel['where'] = $one;
            $dataModel['date'] = $this->requestData['body']['date'];
            $dataModel['type'] = 2;

            $this->newModel[$key] = new BaseModel();
            $this->newModel[$key]->load(['Operations' => $dataModel]);

            if(!$this->newModel[$key]->save()){
                Curl::sendMsgTelegram("create mass transaction {$this->needModel[$key]['id']} , " . implode(' | ',$this->newModel->getErrorSummary(true)) , 'alert');

                return false;
            }

            $this->newModel[$key]['transaction'] = $this->needModel[$key]['transaction'];

            if(!$this->newModel[$key]->update()){
                Curl::sendMsgTelegram("create mass transaction {$this->needModel[$key]['id']} , " . implode(' | ',$this->newModel->getErrorSummary(true)) , 'alert');

                return false;
            }
        }
        $this->clearMassData();
    }
    //создания корректировки
    private function adjustment()
    {
        $model_Operations = new BaseModel();
        $model_Operations->date = date("Y-m-d H:i:s");
        $model_Operations->status = 2;
        $model_Operations->type = 3;
        $model_Operations->where = 1;
        $model_Operations->whence = 1;
        $model_Operations->save();
        $model_Operations->transaction = $this->needModel['transaction']['transaction'];
        $model_Operations->update();

        foreach ($this->requestData['body']['Id'] as $key => $id) {
            $product = Product::findOne($id);
            //проверяем наличия данных 
            if($this->requestData['body']['Amount'][$key] != ''){

                $product->amount = (int)$this->requestData['body']['Amount'][$key];

            }else{
                $error[$product->id] = 'Amount не передано ' . $product->id; 
            }

            if($this->requestData['body']['Start_price'][$key] != ''){
                $product->start_price = $this->requestData['body']['Start_price'][$key];

            }else{
                $error[$product->id] = 'Start_price не передано ' . $product->id; 
            }

            if($this->requestData['body']['Cost_price'][$key] != ''){

                $product->cost_price = $this->requestData['body']['Cost_price'][$key];

            }else{
                $error[$product->id] = 'Cost_price не передано' . $product->id; 
            }

            if($this->requestData['body']['Trade_price'][$key] != ''){

                $product->trade_price = $this->requestData['body']['Trade_price'][$key];

            }else{
                $error[$product->id] = 'Trade_price не передано' . $product->id; 
            }
            
            $product->date_adjustment = $this->needModel['transaction']['date']; 
            
            if($product->save() && !isset($error)){
                OperAdjustment::saveRow([
                    'amount' => $product->amount,
                    'cost_price' => $product->cost_price,
                    'start_price' => $product->start_price,
                    'trade_price' => $product->trade_price,
                    'transaction_id' => $model_Operations->id,
                    'product_id' => $id
                ]);
            }else{
                Curl::sendMsgTelegram("transaction adjustment {$model_Operations->transaction} product {$product->id} , not save" . implode(' | ' , $product->getErrorSummary(true)) , 'alert');

                return false;
            }
        }
    }
    //обновления приход && расход(одиночный) || вызов updateMass
    private function update()
    {
        if($this->requestData['title'] == 'confirm-mass-transaction'){
            return $this->updateMass();
        }else{
            $this->newModel = BaseModel::findOne($this->needModel['id']);
            $this->newModel->transaction = $this->needModel['transaction'];
            $this->newModel->status = 1;

            if(!$this->newModel->update()){
                Curl::sendMsgTelegram("update transaction {$this->needModel['id']} , " . implode(' | ',$this->newModel->getErrorSummary(true)) , 'alert');

            }else{
                $this->clearData($this->needModel,$this->newModel);
            }
        }
    }
    //обновления масс расхода
    private function updateMass()
    {
        foreach($this->requestData['body'] as $key => $id){
            $this->newModel[$key] = BaseModel::findOne($id);
            $this->newModel[$key]->transaction = $this->needModel[$key]['transaction'];
            $this->newModel[$key]->status = 1;

            if(!$this->newModel[$key]->update()){
                Curl::sendMsgTelegram("update mass transaction {$this->needModel[$key]['id']} , " . implode(' | ' , $this->newModel->getErrorSummary(true)) , 'alert');

                return false;
            }
        }
        $this->clearMassData();
    }
    //удаления масс расхода
    private function deleteMass()
    {
        foreach($this->requestData['body'] as $one){
            $this->delete(['id' => $one]);
        }
    } 
    //удаления приход расход(одиночный)
    private function delete($data = null)
    {
        if(is_null($data)){
            $conditional = ['transaction' => $this->requestData['body']['transaction']];
        }else{
            $conditional = ['id' => $data['id']];
        }

        $transaction = BaseModel::find()->where($conditional)->one();

        if(!empty($transaction)){
            $transaction->delete();
        }else{
            $logData = $conditional['transaction'] ?? $conditional['id'];

            Curl::sendMsgTelegram('telegram', [ 
                'message' => "transaction {$logData} , not found",
                'type' => 'alert'
            ]);

            return false;
        }
    }

    private function stopEdit()
    {
        $this->newModel = BaseModel::find()->where(['id' => $this->requestData['body']['id']])->one();
        $this->newModel->status = 2;

        if(!$this->newModel->update()){
            Curl::sendMsgTelegram("update transaction {$this->needModel['id']} , " . implode(' | ',$this->newModel->getErrorSummary(true)) , 'alert');

            return false;
        }
    }
    //проверка масс расхода
    private function clearMassData()
    {
        foreach($this->needModel as $oneTransactionKey => $oneTransaction){
            $this->clearData($oneTransaction,$this->newModel[$oneTransactionKey]->getAttributes());
        }
    }
    //проверка одиночный транзакций 
    private function clearData($oldData,$newData)
    {
        foreach($oldData as $key => $one){
            if(in_array($key,$this->continueKey)){ 
                continue;
            }
            if(in_array($key, $this->keySliceTransaction)){
                $newData[$key] = $this->truncate_number($newData[$key],\Yii::$app->params['float']);
                $oldData[$key] = $this->truncate_number($oldData[$key],\Yii::$app->params['float']);
            }

            if($newData[$key] != $oldData[$key]){
                Curl::sendMsgTelegram(
                    "transaction {$oldData['id']} , new => {$newData[$key]} , old => {$oldData[$key]} , key => $key",
                    $this->getTypeError($newData[$key],$oldData[$key],$this->keyType[$key] ?? '')

                );
            }
        }
    }
}
