<?php

namespace app\models;

use yii\helpers\Json;
use GuzzleHttp\Client;
use Yii;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7;

class Curl {

    public static function curl($method, $url, $params = []) {
        $client = new Client();
        if ($method === 'GET') {
            $body = [
                'query' => $params
            ];
        }
        if ($method === 'POST') {
            $body = [
                'form_params' => $params
            ];
        }
        // 88
        try {
            $res = $client->request($method, Yii::$app->params['baseApiUrl'] . $url, $body);
            return ['status' => $res->getStatusCode(), 'body' => $res->getBody()];
        } catch (RequestException $e) {
            return ['status' => $e->getResponse()->getStatusCode()];
        }
    }

}
