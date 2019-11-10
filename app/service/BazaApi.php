<?php

namespace app\service;

use Psr\Log\InvalidArgumentException;
use Webmozart\Assert\Assert;

class BazaApi
{
    const MODEL_PRODUCT = 'product';
    const MODEL_TRANSACTION = 'transaction';
    const MODEL_REQUEST = 'request';

    /**
     * константы записываються в поле title при обновления request
     */
    const REQUEST_TITLE_MANAGER_ADD_PRODUCT = 'manager_add_product';
    const REQUEST_TITLE_MANAGER_EDIT_PRODUCT = 'manager_edit_product';
    const REQUEST_TITLE_MANAGER_CONFIRM_REQUEST = 'manager_confirm_request';
    const REQUEST_TITLE_ADMIN_DELETE_PRODUCT = 'admin_delete_product';
    const REQUEST_TITLE_CREATE_TRANSACTION = 'create_transaction_from_request';
    const REQUEST_TITLE_DELETE_TRANSACTION_EMPTY = 'delete_transaction_empty';
    const REQUEST_TITLE_CLEAR_REQUEST_ADMIN = 'clear-request-admin';

    const PRODUCT_TITLE_CHANGE_STATUS = 'change_status';
    const PRODUCT_TITLE_STATUS_VIEW_MANAGER = 'view_manager';
    const PRODUCT_TITLE_STATUS_PUBLISH = 'publish_status';

    const TRANSACTION_TITLE_COMING = 'coming';
    const TRANSACTION_TITLE_CONSUMPTION = 'consumption';
    const TRANSACTION_TITLE_CONFIRM = 'confirm-transaction';
    const TRANSACTION_TITLE_ADD_PRODUCT_CONSUMPTION_TRANSACTION = 'add-product-consumption-transaction';
    const TRANSACTION_TITLE_ADD_PRODUCT_COMING_TRANSACTION = 'add-product-coming-transaction';
    const TRANSACTION_TITLE_LIVE_EDIT = 'live-edit';
    const TRANSACTION_TITLE_DELETE_PRODUCT_COMING = 'delete-product-coming';
    const TRANSACTION_TITLE_DELETE_PRODUCT_CONSUMPTION = 'delete-product-consumption';
    const TRANSACTION_TITLE_SEND_ARCHIVE = 'transaction-send-archive';
    const TRANSACTION_TITLE_SEND_ARCHIVE_MASS_TRANSACTION = 'mass-transaction-send-archive';
    const TRANSACTION_TITLE_STOP_EDIT = 'stop-edit';
    const TRANSACTION_TITLE_SAVE_NEW_OPERATION = 'save-new-transaction';
    const TRANSACTION_TITLE_CANCEL_EDIT = 'cancel-edit';
    const TRANSACTION_TITLE_CREATE_MASS_TRANSACTION = 'create-mass-transaction';
    const TRANSACTION_TITLE_ADD_PRODUCT_MASS_TRANSACTION = 'add-product-mass-transaction';
    const TRANSACTION_TITLE_DELETE_PRODUCT_MASS_TRANSACTION = 'delete-product-mass-transaction';
    const TRANSACTION_TITLE_CONFIRM_MASS_TRANSACTION = 'confirm-mass-transaction';
    const TRANSACTION_TITLE_DELETE_MASS_TRANSACTION = 'delete-mass-transaction';
    const TRANSACTION_TITLE_DELETE_MASS_TRANSACTION_PRODUCT = 'delete-mass-transaction-product';
    const TRANSACTION_TITLE_ADJUSTMENT = 'adjustment-transaction';
    const CHANGE_COURSE = 'change-course';

    const MODEL_TRANSACTION_PRODUCT = 'transaction-product';
    const REQUEST_TITLE_CREATE_EMPTY_TRANSACTION = 'create-empty-transaction';
    const MODEL_REQUEST_PRODUCT = 'request-product';

    const TYPE_CREATE = 'create';
    const TYPE_UPDATE = 'update';
    const TYPE_DELETE = 'delete';

    const VERSION = 'release-2.2';


    private $url;
    private $model;
    private $type;

    public function __construct($model = null,$type = null)
    {
        if(!isset(\Yii::$app->params['targetSync']) || empty(\Yii::$app->params['targetSync'])){
            throw new \Exception('targetSync');
        }

        $domen = \Yii::$app->params['domenApi'];

        if($model !== null){
            Assert::oneOf($model,[
                self::MODEL_REQUEST,
                self::MODEL_REQUEST_PRODUCT,
                self::MODEL_PRODUCT,
                self::MODEL_TRANSACTION,
                self::MODEL_TRANSACTION_PRODUCT

            ]);
        }

        if($type !== null){
            Assert::oneOf($type,[
                self::TYPE_CREATE,
                self::TYPE_DELETE,
                self::TYPE_UPDATE
            ]);
        }

        $this->url = "{$domen}/api/new-baza/";

        $this->model = $model;
        $this->type = $type;
    }

    public function add($data = null)
    {
        if($this->ping()) {

            Assert::notEmpty($this->model);
            Assert::notEmpty($this->type);

            $this->isFullData($data);

            $url = $this->url . 'add/'. $this->model . '/' . $this->type;

            $dataForSend = http_build_query($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataForSend);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $answer = curl_exec($ch);

            curl_close($ch);
            return $answer;
        }
    }

    public function count()
    {
        if($this->ping()){

            $url = $this->url . 'check';

            if($this->model !== null){
                $url .= '/' . $this->model;
            }

            if($this->type !== null){
                Assert::notEmpty($this->model);
                $url .= '/' . $this->type;
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $answer = curl_exec($ch);

            curl_close($ch);

            $std = json_decode($answer);

            return $std->count;
        }

    }

    public function getData($limit = null)
    {
        if($this->ping()) {
            $url = $this->url . 'get-data';

            if($this->model !== null){
                $url .= '/' . $this->model;
            }

            if($this->type !== null){
                Assert::notEmpty($this->model);
                $url .= '/' . $this->type;
            }

            if($limit !== null){
                Assert::integer($limit);
                $url .= '?limit=' . $limit;
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $answer = curl_exec($ch);

            curl_close($ch);

//            $std = json_decode($answer);

            return $answer;
        }
    }

    public function delete(array $ids)
    {
        if($this->ping()) {
            Assert::notEmpty($ids);
            Assert::isArray($ids);

            $url = $this->url . 'delete';


            $dataForSend = http_build_query($ids);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataForSend);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $answer = curl_exec($ch);

            curl_close($ch);

            return $answer;
        }
    }

    /**
     * проверка соединения
     * @return bool
     */
    private function ping()
    {   
        $target = \Yii::$app->params['targetSync'];

        if(!isset($target) || empty($target)){
            return false;
        }else{
            if(self::VERSION !== $target){
                return false;
            }
        }

        $url = \Yii::$app->params['domenApi'];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $answer = curl_exec($ch);
//
//        curl_close($ch);
//
//        if(!empty($answer)){
//            $std = json_decode($answer);
//            if($std->name === 'App API' && $std->version === '1.0'){

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if(($status == 200 || $status == 201) && !empty($answer)){
            $std = json_decode($answer);

            if($this->isApi($std)){

                return true;
            }
            return false;
        }
        return false;
    }

    private function isFullData($data)
    {
        if(array_key_exists('data',$data) && array_key_exists('requestData',$data)) {
            return;
        }
        throw new InvalidArgumentException('В передаваемых данных отсутствуют данные по \'data\' или \'requestData\'.');
    }

    private function isApi($std)
    {
        return $std->name === 'App API' && $std->version === '1.0';
    }
}