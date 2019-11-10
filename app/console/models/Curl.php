<?php
namespace app\console\models;

use app\console\models\BaseSyncModel;
use yii\helpers\Json;

class Curl extends BaseSyncModel
{
    public static function GetRequestGet(string $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, \Yii::$app->params['domenApi'] . '/api/' . $url);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $output = curl_exec($ch);
        $statusCode = curl_getinfo($ch);
        $typeCode = substr($statusCode['http_code'], 0,1);

        curl_close($ch);
        if($typeCode == 2 || $typeCode == 1){
            try{
                return Json::decode($output);
            }catch(\Exception $e){
                self::sendMsgTelegram($e->getMessage() . ' url : ' . $url . ' fail request','alert');
            }
        }elseif(empty($statusCode['http_code'])){
            self::sendMsgTelegram(' url : ' . $url . ' status code is empty' ,'warning');
        }else{
            self::sendMsgTelegram(' url : ' . $url . ' status code is ' .  $statusCode['http_code'],'warning');
        }
    }

    public static function SendPostRequest(string $url,array $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, \Yii::$app->params['domenApi'] . '/api/' . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $typeCode = substr($statusCode, 0,1);

        curl_close($ch);
        if($typeCode == 2 || $typeCode == 1){
            try{
                return Json::decode($response);
            }catch(\Exception $e){
                self::sendMsgTelegram($e->getMessage() . ' url : ' . $url . ' fail request' , 'alert');
            }
        }elseif(empty($statusCode['http_code'])){
            self::sendMsgTelegram(' url : ' . $url . ' status code is empty' , 'warning');
        }else{
            self::sendMsgTelegram(' url : ' . $url . ' status code is ' .  $statusCode['http_code'] , 'warning');
        }
    }

    public static function sendMsgTelegram($message,$type = 'alert')
    {
        return Curl::SendPostRequest('telegram', [
            'status' => $type,
            'message' => $message
        ]);
    }
}