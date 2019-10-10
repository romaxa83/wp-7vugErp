<?php
namespace app\console\controllers;

use app\console\models\Curl;
use app\console\models\Product;
use app\console\models\Transaction;
use app\console\models\TransactionProduct;
use app\console\models\RequestManager;
use app\console\models\RequestProductManager;
use app\console\models\BaseSyncModel;

date_default_timezone_set('Europe/Kiev');

class SyncronizationController extends \yii\console\Controller
{
    private $limit = 10;

    public function actionCheckData()
    {   
        $row = Curl::GetRequestGet('old-baza/check');
        $data = Curl::GetRequestGet("old-baza/get-data?limit=$this->limit");

        if($row['count'] > 0){
            if(!file_exists(\Yii::$app->getBasePath() . '/console/syncronization.lock')){
                $fp = fopen( \Yii::$app->getBasePath() . '/console/syncronization.lock', 'a' );
                
                \Yii::$app->params['debbug'] ? Curl::sendMsgTelegram("start read data. count : " . count($data['data']), 'stage syncronization : 1') : null;
                
                foreach ($data['data'] as $one){
                    Curl::sendMsgTelegram('get row id: ' . $one['id'] . PHP_EOL . 'action : ' . $one['action'] . ' model :  ' . $one['model'], 'stage syncronization : 2');
                    
                    if(is_null($this->distributor($one))){                    
                        Curl::SendPostRequest('old-baza/delete', ['id' => $one['id']]);
                        \Yii::$app->params['debbug'] ? Curl::sendMsgTelegram('finish with out critical error row id : ' . $one['id'], 'stage syncronization : 3') : null;
                    }else{
                        \Yii::$app->params['debbug'] ? Curl::sendMsgTelegram('finish with critical error row id : ' . $one['id'], 'stage syncronization : 3') : null;

                        return false;
                    }
                }
                
                if(fclose( $fp )){
                    if(!unlink( \Yii::$app->getBasePath() . '/console/syncronization.lock' )){
                        Curl::sendMsgTelegram('file not deleted');
                    }
                }else{
                    Curl::sendMsgTelegram('file not closed');
                }
            }else{
                Curl::sendMsgTelegram('file lock exist. Last id : ' . array_shift($data['data'])['id']);
            }
        }
    }
    
    public function actionPingApi()
    {
        $row = Curl::GetRequestGet('old-baza/check');
        if(isset($row['count'])){
            echo 'status : success' . PHP_EOL;
        }else{
            echo 'error' . PHP_EOL;
        }
    }

    public function actionUnlock()
    {
        if(file_exists(\Yii::$app->getBasePath() . '/console/syncronization.lock')){
            unlink( \Yii::$app->getBasePath() . '/console/syncronization.lock' );
            echo 'success' . PHP_EOL;
        }else{
            echo 'file not exists' . PHP_EOL;
        }
    }

    public function actionCheckLock()
    {
        echo file_exists(\Yii::$app->getBasePath() . '/console/syncronization.lock') ? 'lock' : 'un lock' . PHP_EOL;
    }

    public function actionClearData()
    {
        Curl::GetRequestGet('old-baza/clear');
    }

    private function distributor($one)
    {
        switch (true){
            case isset($one['requestData']['title']) && $one['requestData']['title'] === 'change-course' : 
                $result = (new BaseSyncModel())->changeCourse($one);
            break;
            case $one['model'] == 'product' : 
                $result = (new Product($one))->entry();
            break;
            case $one['model'] == 'transaction' : 
                $result = (new Transaction($one))->entry();
            break;
            case $one['model'] == 'transaction-product' : 
                $result = (new TransactionProduct($one))->entry();
            break;
            case $one['model'] == 'request' : 
                $result = (new RequestManager($one))->entry();
            break;
            case $one['model'] == 'request-product' :
                $result = (new RequestProductManager($one))->entry();
            break;

            default : 
                $result = false; 
                Curl::sendMsgTelegram('not entry to model');
            break;
        }
        return $result;
    }
}
