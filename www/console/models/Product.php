<?php
namespace app\console\models;

use app\console\models\BaseSyncModel;
use app\models\Product as BaseModel;
use app\models\Agent;

class Product extends BaseSyncModel
{
    private $action;
    private $needModel;
    private $newModel;
    private $requestData;
    //не соответствующие ключи (старая => новая)
    private $brokenKey = [
        'id_category' => 'category_id',
        'id_agent' => 'agent_id'
    ];
    //ключи которые пропускаем 
    private $continueKey = [
        'vendor_code',
        'created_at',
        'updated_at',
        'change_price'
    ];
    //ключи за которыми могут лежать null 
    private $nullField = [
        'view_manager',
        'publish_status'
    ];

    public function __construct(array $data) 
    {
        $this->action = $data['action'];
        $this->needModel = $data['data'];
        $this->requestData = $data['requestData'];
        
        if(isset($data['requestData']['id_agent'])){
            $agent = Agent::find()->asArray()->where(['firm' => $data['requestData']['id_agent']])->one();
        
            if(empty($agent)){
                Curl::sendMsgTelegram("агент не найден , id => {$data['requestData']['id_agent']}" , 'alert');

                return false;
            }

            $this->requestData['agent_id'] = $agent['id'];
        }

        if(isset($data['requestData']['id_category'])){
            $this->requestData['category_id'] = $data['requestData']['id_category'];
        }
    }

    public function entry()
    {
        switch (true){
            case $this->action === 'create' : 
                return $this->create();
            break;

            case $this->action === 'delete' || ($this->action === 'update' && isset($this->requestData['title']) && $this->requestData['title'] === 'view_manager') : 
                return $this->updateStatus($this->action === 'delete' ? 'status' : 'view_manager');
            break;

            case $this->action === 'update' : 
                return $this->update();
            break;
        }
    }
    
    private function create() 
    {
        $this->newModel = new BaseModel();
        $this->requestData['status'] = (isset($this->requestData['status']) && $this->requestData['status'] === 'on') ? 1 : 0;
        $this->newModel->load(['Product' => $this->requestData]);

        if($this->newModel->save()){
            $this->clearData();
        }else{
            Curl::sendMsgTelegram("product {$this->needModel['id']} , " . implode(' | ' , $this->newModel->getErrorSummary(true)) , 'alert');

            return false;
        }
    }
    
    private function update()
    {
        $this->newModel = BaseModel::find()->where(['id' => $this->needModel['id']])->one();
        $this->requestData['status'] = (isset($this->requestData['status']) && $this->requestData['status'] === 'on') ? 1 : 0;
        
        $this->needModel['change_price'] = $this->newModel['change_price'];

        if(!empty($this->newModel->attributes)){
            unset($this->requestData['vendor_code']);

            $this->newModel->load(['Product' => $this->requestData]);

            if($this->newModel->update()){
                $this->clearData();
            }else{
                Curl::sendMsgTelegram("product {$this->needModel['id']} , " . implode(' | ' , $this->newModel->getErrorSummary(true)) , 'alert');

                return false;
            }
        }else{
            Curl::sendMsgTelegram("product {$this->needModel['id']} , not found" , 'alert');

            return false;
        }
    }
    
    private function updateStatus(string $type)
    {
        $this->newModel = BaseModel::find()->where(['id' => $this->needModel['id']])->one();

        if(!empty($this->newModel)){
            $this->newModel->{$type} = $this->requestData['status'] ?? $this->requestData['body']['check'];
            $this->newModel->update();
        }else{
            Curl::sendMsgTelegram("product {$this->needModel['id']} , not found" , 'alert');
            
            return false;
        }
    }
    
    private function clearData()
    {
        foreach($this->needModel as $key => $one){
            if(in_array($key,$this->continueKey) || (in_array($key, $this->nullField) && is_null($this->newModel->getAttributes()[$key]))){ 
                continue; 
            }elseif($key == 'id_category' || $key == 'id_agent'){
                $key = $this->brokenKey[$key];
            }
            
            if($this->newModel->getAttributes()[$key] != $one){
                Curl::sendMsgTelegram(
                    "product {$this->needModel['id']} , new => {$this->newModel->getAttributes()[$key]} , old => $one , key => $key" , 
                    $this->getTypeError($this->newModel->getAttributes()[$key],$one)
                );
            }
        }
    }
}