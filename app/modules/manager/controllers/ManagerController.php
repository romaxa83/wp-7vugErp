<?php

namespace app\modules\manager\controllers;

use Yii;
use app\models\Product;
use app\modules\manager\models\Request;
use app\modules\manager\models\RequestProduct;
use app\controllers\BaseController;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\filters\AccessControl;
use app\controllers\AccessController;
use app\service\BazaApi;

class ManagerController extends BaseController 
{   
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    AccessController::getAccessRules(Yii::$app->controller->id),
                    [
                        'allow' => true,
                        'roles' => ['manager']
                    ]
                ]
            ]
        ];
    }
    //стартовая точка для менеджеров
    public function actionIndex() 
    {
        $this->checkAuthKey();
        $dataProviderManager = new ActiveDataProvider([
            'query' => Product::find()->where(['and', ['view_manager' => 1], ['status' => 1]])->orderBy(['id' => SORT_DESC])
        ]);
        return $this->render('index', [
            'dataProviderManager' => $dataProviderManager,
        ]);
    }
    //просмотр и редактирования заявки 
    public function actionShowRequest() 
    {
        $this->checkAuthKey();
        $request = Request::find()->where(['store_id' => Yii::$app->session->get('getStoreId')])->one();
        $model = new RequestProduct(['scenario' => RequestProduct::NEW_ROW]);
        return $this->render('show', ['model' => $model, 'request' => $request]);
    }
    //добавления базовых товаров +
    public function actionAddProduct() 
    {
        $this->checkAuthKey();
        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $model = new RequestProduct(['scenario' => RequestProduct::NEW_ROW]);
            if($model->load($post)){
                $result = $model->saveProduct(Yii::$app->session->get('getStorePrice'));
                if($result['status'] == RequestProduct::SUCCESS_ADD){

                    /*****___SEND_TO_API___ ******/
                    $dataApi['requestData']['title'] = BazaApi::REQUEST_TITLE_MANAGER_ADD_PRODUCT;
                    $dataApi['requestData']['body'] = $post['RequestProduct'];
                    $dataApi['data'] = $result['model']->getAttributes();
                    (new BazaApi('request-product','create'))->add($dataApi);

                    return JSON::encode([
                        'type' => RequestProduct::SUCCESS_ADD, 
                        'view' => $this->renderPartial('table-tr', ['one' => $result['model']])
                    ]);
                }elseif($result['status'] == RequestProduct::ERROR_ADD){
                    return JSON::encode([
                        'type' => RequestProduct::ERROR_ADD,
                        'msg' => FormattedMessenge($result['model']->getErrorSummary(true))
                    ]);
                }elseif($result['status'] == RequestProduct::DUPLICATE_ADD){
                    $result['model']->name = $result['model']->getProduct()->one()->name;
                    return JSON::encode([
                        'type' => RequestProduct::DUPLICATE_ADD, 
                        'view' => $this->renderPartial('_form-adjustment-amount', ['model' => $result['model']])
                    ]);
                }
            }
        }
    }
    //Метод меняет в заявке кол-во товара при редактировании
    public function actionAdjustmentAmount($data = null)
    {
        $this->checkAuthKey();
        $post = ($data === null) ? Yii::$app->request->post() : $data; 
        $result = RequestProduct::ChangeAmount($post['RequestProduct']['product_id'],$post['RequestProduct']['request_id'],$post['RequestProduct']['amount']);
        if($result['status']){

            /*****___SEND_TO_API___ ******/
            $dataApi['requestData']['title'] = BazaApi::REQUEST_TITLE_MANAGER_EDIT_PRODUCT;
            $dataApi['requestData']['body'] = $post['RequestProduct'];
            $dataApi['data'] = $result['model']->getAttributes();
            (new BazaApi('request-product','update'))->add($dataApi);

            return Json::encode(['status' => true,'value' => $result['model']->amount,'id' => $result['model']->product_id]);
        }else{
            return Json::encode(['status' => false,'error' => FormattedMessenge($result['model']->getErrorSummary(true))]);
        }
    }
    //Метод потверждает формирование заявки,меняя статус и записывая комментарии
    public function actionConfirmRequest()
    {
        $this->checkAuthKey();
        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $request = Request::findOne($post['request_id']);
            if(!empty($request)){
                $request->confirmRequest($post['comment']);

                /*****___SEND_TO_API___ ******/
                $dataApi['requestData']['title'] = BazaApi::REQUEST_TITLE_MANAGER_CONFIRM_REQUEST;
                $dataApi['requestData']['body'] = $post;
                $dataApi['data'] = $request->getAttributes();
                (new BazaApi('request','update'))->add($dataApi);

                return JSON::encode(['status' => true]);
            }else{
                return JSON::encode(['status' => false]);
            }
        }
    }
}