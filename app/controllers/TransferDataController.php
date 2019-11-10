<?php
namespace app\controllers;
                        
use Yii;
use app\models\OperComing;
use app\models\OperConsumption;
use app\models\Archive;
use app\models\ArchiveValue;
use app\modules\manager\models\Request;
use yii\helpers\Json;
use app\models\Product;

class TransferDataController extends BaseController 
{
    
    public $limit = 1;
    
    public function init() 
    {
        parent::init();
        ini_set('max_execution_time', 100);
    }
    //ok
    public function actionTransferProdValueDb()
    {
        if(Yii::$app->request->isAjax) {
            $i = (int)Yii::$app->request->post('i');
            $operations = Yii::$app->db->createCommand("SELECT * FROM operations LIMIT ". $this->limit ." OFFSET ". $this->limit * $i ." ")
                ->queryAll();
            
            if (!sizeof($operations)) {
                $res['finish'] = true;
                return json_encode($res);
            }
            foreach ($operations as $operation) {
                try {
                    $prod_value = self::getArrayForJSONs($operation['prod_value']);
                    if(!empty($prod_value)) {
                        switch($operation['type']){
                            case 1 :
                                foreach ($prod_value as $one){
                                    $model = new OperComing();
                                    $model->transfer = true;
                                    $model->transaction_id = $operation['id'];
                                    $model->product_id = substr($one['product'],1);
                                    $model->vproduct_id = null;
                                    $model->amount = $this->CheckValue($one,'amount','transaction',$operation['id'],$model->product_id);
                                    $model->price1 = $this->CheckValue($one,'price1','transaction',$operation['id'],$model->product_id);
                                    $model->price2 = $this->CheckValue($one,'price2','transaction',$operation['id'],$model->product_id);
                                    $model->start_price = $this->CheckValue($one,'start_price','transaction',$operation['id'],$model->product_id);
                                    $model->cost_price = $this->CheckValue($one,'cost_price','transaction',$operation['id'],$model->product_id);
                                    if ($model->price1 <= 0) {
                                        $pr = Product::find()->asArray()->where(['id' => $model->product_id])->one();
                                        $model->price1 = $pr['price1'];
                                    }
                                    if ($model->price2 <= 0) {
                                        $pr = Product::find()->asArray()->where(['id' => $model->product_id])->one();
                                        $model->price2 = $pr['price2'];
                                    }
                                    $temp = Yii::$app->db->createCommand("SELECT * FROM temp WHERE product_id = ". $model->product_id ." AND transaction_id = ". $operation['id'] ."")
                                        ->queryOne();
                                    if($temp){
                                        $model->old_cost_price = $temp['old_cost_price'];
                                        $model->old_amount = $temp['old_amount'];
                                    }else{
                                        $this->WriteLog('/error','transaction','row table `Temp` undefined, product_id : '. $model->product_id .' transaction_id = '. $operation['id']);
                                    }
                                    if($model->save()){
                                        $this->WriteTransferData($model,'coming transaction');
                                    }else{
                                        $this->WriteLog('/error','transaction','row transaction coming error save ' . implode(' | ', $model->getErrorSummary(true)) . ' | product_id : '. $model->product_id .' transaction_id = '. $operation['id']);
                                    }
                                }
                                break;
                            case 2 :
                                foreach ($prod_value as $one){
                                    $model = new OperConsumption();
                                    $model->transfer = true;
                                    $model->transaction_id = $operation['id'];
                                    $model->product_id = substr($one['product'],1);
                                    $model->vproduct_id = null;
                                    $model->amount = $this->CheckValue($one,'amount','transaction',$operation['id'],$model->product_id);
                                    $model->price = $this->CheckValue($one,'price','transaction',$operation['id'],$model->product_id);
                                    $model->trade_price = $this->CheckValue($one,'trade_price','transaction',$operation['id'],$model->product_id);
                                    $model->cost_price = $this->CheckValue($one,'cost_price','transaction',$operation['id'],$model->product_id);
                                    if($model->save()){
                                        $this->WriteTransferData($model,'consumption transaction');
                                    }else{
                                        $this->WriteLog('/error','transaction','row transaction consumption error save ' . implode(' | ', $model->getErrorSummary(true)) . ' | product_id : '. $model->product_id .' transaction_id = '. $operation['id']);
                                    }
                                }
                                break;
                            case 3 :
                                foreach ($prod_value as $one){
                                    $model = new \app\models\OperAdjustment();
                                    $model->transaction_id = $operation['id'];
                                    $model->product_id = substr($one['product'],1);
                                    $model->vproduct_id = null;
                                    $model->amount = $this->CheckValue($one,'amount','transaction',$operation['id'],$model->product_id);
                                    $model->trade_price = $this->CheckValue($one,'trade_price','transaction',$operation['id'],$model->product_id);
                                    $model->cost_price = $this->CheckValue($one,'cost_price','transaction',$operation['id'],$model->product_id);
                                    $model->start_price = $this->CheckValue($one,'start_price','transaction',$operation['id'],$model->product_id);
                                    if($model->save()){
                                        $this->WriteTransferData($model,'adjustment transaction');
                                    }else{
                                        $this->WriteLog('/error','transaction','row transaction adjustment error save ' . implode(' | ', $model->getErrorSummary(true)) . ' | product_id : '. $model->product_id .' transaction_id = '. $operation['id']);
                                    }
                                }
                                break;
                            default: break;
                        }
                    }else{
                        $this->WriteLog('/error','transaction','[empty], transaction_id = '. $operation['id']);
                    }
                } catch (\Exception $e){
                    $this->WriteLog('/error','transaction', $e . 'transaction_id = ' . $operation['id']);
                    continue;
                }
            }
            
            $i++;
            $res['i'] = $i;
            
            return json_encode($res);
        }
    }
    //ok
    public function actionClearOperations()
    {
        if(Yii::$app->request->isAjax){
            $id = $this->getId('transaction');
            if($id){
                $empty = implode(',', $id['empty']);
                $error = implode(',', $id['error']);
                if(!empty($empty)){
                    Yii::$app->db->createCommand("DELETE FROM operations WHERE id IN ({$empty})")->execute();
                }
                if(!empty($error)){
                    Yii::$app->db->createCommand("UPDATE operations SET prod_value = '' WHERE id NOT IN ({$error})")->execute();
                }else{
                    Yii::$app->db->createCommand("UPDATE operations SET prod_value = '' ")->execute();
                }    
                return true;
            }else{
                return false;
            }
        }
    }
    //ok
    public function actionTransferOperArchiveTabelDb()
    {
        if(Yii::$app->request->isAjax) {
            $i = (int)Yii::$app->request->post('i');
            $operations = Yii::$app->db->createCommand("SELECT * FROM oper_archive LIMIT ". $this->limit ." OFFSET ". $this->limit * $i ." ")
                ->queryAll();
            
            if (!sizeof($operations)) {
                $res['finish'] = true;
                return json_encode($res);
            }

            foreach ($operations as $operation) {
                $archive = new Archive();
                
                try{
                    $value = unserialize($operation['value']);
                }catch(\Exception $e){
                    $this->WriteLog('/error','archive','error unserialize , archive_id = '. $operation['id']);
                    continue;
                }
                
                $archive->type = $operation['type'];
                $archive->transaction_id = $operation['id_transaction'];
                $archive->transaction = $operation['transaction'];
                $archive->whence = $this->CheckValue($value,'whence','archive',$operation['id']);
                $archive->where = $this->CheckValue($value,'where','archive',$operation['id']);
                $archive->total_usd = $this->CheckValue($value,'total_usd','archive',$operation['id']);
                $archive->total_ua = $this->CheckValue($value,'total_usd','archive',$operation['id']);
                $archive->date = $operation['date'];
                $archive->date_archive = $operation['date_archive'];
                if($archive->save()){
                    $this->WriteTransferData($archive,'archive');
                }else{
                    $this->WriteLog('/error','archive','row archive error save '  . implode(' | ', $archive->getErrorSummary(true)) .  ' | archive_id = '. $operation['id']);
                }
                
                $prod_value = self::getArrayForJSONs($value['prod_value']);
                
                foreach ($prod_value as $one){
                    $archive_val = new ArchiveValue();
                    $archive_val->archive_id = $archive->id;
                    $archive_val->vproduct_id = null;
                    $archive_val->product_id = (int) substr($one['product'], 1);
                    $archive_val->amount = $this->CheckValue($one,'amount','archive',$operation['id'],$archive_val->product_id);
                    $archive_val->price = $this->CheckValue($one,'price','archive',$operation['id'],$archive_val->product_id);
                    $archive_val->price1 = $this->CheckValue($one,'price1','archive',$operation['id'],$archive_val->product_id);
                    $archive_val->price2 = $this->CheckValue($one,'price2','archive',$operation['id'],$archive_val->product_id);
                    $archive_val->trade_price = $this->CheckValue($one,'trade_price','archive',$operation['id'],$archive_val->product_id);
                    $archive_val->start_price = $this->CheckValue($one,'start_price','archive',$operation['id'],$archive_val->product_id);
                    $archive_val->cost_price = $this->CheckValue($one,'cost_price','archive',$operation['id'],$archive_val->product_id);
                    if($archive_val->save()){
                        $this->WriteTransferData($archive_val,'archive_value row');
                    }else{
                        $this->WriteLog('/error','archive','archive_value error save ' . implode(' | ', $archive_val->getErrorSummary(true)) . ' | archive_id = '. $operation['id']);
                    }
                }
            }

            $i++;
            $res['i'] = $i;
            
            return json_encode($res);
        }
    }
    //ok
    public function actionClearArchive()
    {
        if(Yii::$app->request->isAjax){
            $id = $this->getId('archive');
            if($id){
                $empty = implode(',', $id['empty']);
                $error = implode(',', $id['error']);
                if(!empty($empty)){
                    Yii::$app->db->createCommand("DELETE FROM oper_archive WHERE id IN ({$empty})")->execute();
                }
                if(!empty($error)){
                    Yii::$app->db->createCommand("UPDATE oper_archive SET value = '' WHERE id NOT IN ({$error})")->execute();
                }else{
                    Yii::$app->db->createCommand("UPDATE oper_archive SET value = '' ")->execute();
                }    
                return true;
            }else{
                return false;
            }
        }
    }
    //ok
    public function actionTransferProductRequestTabelDb()
    {
        if(Yii::$app->request->isAjax) {
            $i = (int)Yii::$app->request->post('i');
            $operations = Yii::$app->db->createCommand("SELECT * FROM product_request LIMIT ". $this->limit ." OFFSET ". $this->limit * $i ." ")
                ->queryAll();
            
            if (!sizeof($operations)) {
                $res['finish'] = true;
                return json_encode($res);
            }

            foreach ($operations as $operation) {
                $Request = new Request();
                $Request->comment = $operation['comment'];
                $Request->store_id = $operation['store_id'];
                $Request->status = $operation['status'];
                $Request->created_at = $operation['created_at'];
                $Request->updated_at = $operation['updated_at'];
                if($Request->save()){
                    $this->WriteTransferData($Request,'request');
                }else{
                    $this->WriteLog('/error','request','row request error save '  . implode(' | ', $Request->getErrorSummary(true)) .  ' | request_id = '. $operation['id']);
                }
                
                $prod_value = self::getArrayForJSONs($operation['prod_value']);
                
                if(!empty($prod_value)){
                    $insert = [];
                    $index = 0;
                    foreach ($prod_value as $one){
                        $insert[$index][] = $Request->id;
                        $insert[$index][] = substr($one['product'], 1);
                        $insert[$index][] = 0;
                        $insert[$index][] = $this->CheckValue($one,'amount','request',$operation['id'],substr($one['product'], 1));
                        $insert[$index][] = $this->CheckValue($one,'price','request',$operation['id'],substr($one['product'], 1));
                        $insert[$index][] = $this->CheckValue($one,'cost_price','request',$operation['id'],substr($one['product'], 1));
                        $insert[$index][] = $this->CheckValue($one,'trade_price','request',$operation['id'],substr($one['product'], 1));
                        $value[] = '(' . implode(',', $insert[$index]) . ')';
                        $index++;
                    }
                    $result = implode(',', $value);
                    Yii::$app->db->createCommand("INSERT INTO request_product (request_id,product_id,vproduct_id,amount,price,cost_price,trade_price) VALUES $result")->execute();
                    Yii::$app->db->createCommand("UPDATE request_product SET vproduct_id = NULL")->execute();
                }else{
                    $this->WriteLog('/error','request_product','[empty], transaction_id = '. $operation['id']);
                }
            }
            $i++;
            $res['i'] = $i;

            return json_encode($res);
        }
    }
    //ok
    public function actionClearProductRequest()
    {
        if(Yii::$app->request->isAjax){
            $id = $this->getId('request_product');
            if($id){
                $empty = implode(',', $id['empty']);
                $error = implode(',', $id['error']);
                if(!empty($empty)){
                    Yii::$app->db->createCommand("DELETE FROM product_request WHERE id IN ({$empty})")->execute();
                }
                if(!empty($error)){
                    Yii::$app->db->createCommand("UPDATE product_request SET prod_value = '' WHERE id NOT IN ({$error})")->execute();
                }else{
                    Yii::$app->db->createCommand("UPDATE product_request SET prod_value = '' ")->execute();
                }
                return true;
            }else{
                return false;
            }
        }
    }
    
    public function getId($name)
    {
        if(file_exists(Yii::getAlias('@app') . "/web/uploads/transferLog/error/{$name}.txt")){
            $logFile = file_get_contents(Yii::getAlias('@app') . "/web/uploads/transferLog/error/{$name}.txt");
            $explodedData = explode(';',$logFile);
            $id = ['empty' => [],'error' => []]; 
            foreach($explodedData as $oneRow){
                $explodedRow = explode('=',$oneRow);
                if(isset($explodedRow[1])){
                    if(strpos($explodedRow[0], '[empty]')){
                        $id['empty'][] = trim(explode('=',$oneRow)[1]);
                    }else{
                        $id['error'][] = trim(explode('=',$oneRow)[1]);
                    }
                }
            }
            $id['error'] = array_unique($id['error']);
            $id['empty'] = array_unique($id['empty']);   
            return $id;    
        }else{
            return false;
        }        
    }

    public static function getArrayForJSONs($json)
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
    
    private function CheckValue($value,$key)
    {
        if(isset($value[$key]) && $value[$key]){
            if($key == 'amount'){
                $response = (int)$value[$key];
            }elseif(strpos($key,'price')){
                $response = (double)$value[$key];
            }
            return $response ?? $value[$key];
        }else{
            return 0;
        }
    }
    
    private function WriteTransferData($model,$type)
    {
        $str = '';
        foreach($model->attributes() as $oneAttr){
            if($oneAttr == 'id')                continue;
            $str .= $oneAttr . ' : ' . $model->{$oneAttr} . ' | ';
        }
        $this->WriteLog('data',$model->product_id ?? $type, $type . ' | ' . $str);
    }
    
    private function WriteLog($path,$name,$text)
    {
        $baseLogPath = 'uploads/transferLog/';
        if(!file_exists($baseLogPath)){
            mkdir($baseLogPath,0777,true);
        }
        if(!file_exists($baseLogPath . $path)){
            mkdir($baseLogPath . $path,0777,true);
        }
        $fp = fopen($baseLogPath . $path . '/' . $name . '.txt', "a+");
        file_put_contents($baseLogPath . $path . '/' . $name . '.txt',  date('Y-m-d H:i:s') . ' : ' . $text . ';' . PHP_EOL, FILE_APPEND);
        fclose($fp);
    }
}
