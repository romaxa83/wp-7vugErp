<?php

namespace app\modules\manager\controllers;

use Yii;
use app\modules\manager\models\Request;
use app\modules\manager\models\RequestProduct;
use app\modules\manager\models\RequestSearch;
use app\modules\manager\services\TransactionService;
use app\controllers\BaseController;
use yii\helpers\Json;
use yii\base\Module;
use app\controllers\AccessController;
use yii\filters\AccessControl;
use app\service\BazaApi;
use app\models\Operations;

class AdminController extends BaseController
{
    private $transaction_service;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    AccessController::getAccessRules(Yii::$app->controller->id),
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ]
                ]
            ]
        ];
    }
    
    public function __construct($id, Module $module,TransactionService $transaction,array $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->transaction_service = $transaction;
    }
    //список заявок 
    public function actionIndex()
    {
        $searchModel = new RequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    //просмотр заявки 
    public function actionShow($id)
    {
        $model = Request::findOne($id);
        $product = $model->getRowTable();
        return $this->render('show', compact('model','product'));
    }
    //удаления 1 позиций из заявки
    public function actionDeleteRow()
    {
        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $result = RequestProduct::deleteProduct($post['product_id'],$post['request_id']);
                    
            /*****___SEND_TO_API___ ******/
            $dataApi['requestData']['title'] = BazaApi::REQUEST_TITLE_ADMIN_DELETE_PRODUCT;
            $dataApi['requestData']['body'] = $post;
            $dataApi['data'] = '';
            (new BazaApi('request-product','delete'))->add($dataApi);

            return Json::encode($result);
        }        
    }
    //очистка заявки
    public function actionClearRequest()
    {
        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $request = Request::findOne($post['request_id']);
            $request->clearRequest();

            /*****___SEND_TO_API___ ******/
            $dataApi['requestData']['title'] = BazaApi::REQUEST_TITLE_CLEAR_REQUEST_ADMIN;
            $dataApi['requestData']['body'] = $post['request_id'];
            $dataApi['data'] = $request->getAttributes();
            (new BazaApi('request','delete'))->add($dataApi);

            ShowMessenge('Заявка очищена','success');
            return $this->redirect('/manager/admin/index');
        }    
    }    
    /**
     * Метод создает пустой обьект транзакций
     * params: store_id = id агента
    */
    public function actionCreateEmptyTransaction()
    {
        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $transaction_id = $this->transaction_service->createTransaction($post);

            /*****___SEND_TO_API___ ******/
            $dataApi['requestData']['title'] = BazaApi::REQUEST_TITLE_CREATE_EMPTY_TRANSACTION;
            $dataApi['requestData']['body'] = $post;
            $dataApi['data'] = Operations::find()->asArray()->where(['id' => $transaction_id])->one();
            (new BazaApi('request','create'))->add($dataApi);

            return $transaction_id;
        }
    }
    /**
     * Метод наполняет расходную транзакцию на основе товаров из заявки
    */
    public function actionFillingTransaction($data = null)
    {
        $post = ($data == null) ? Yii::$app->request->post() : $data;
        try {
            $result = $this->transaction_service->fillTransaction($post);
            
            if(!empty($result['transaction'])){
                ShowMessenge('Транзакция создана № ' . $result['transaction'], 'success');
            }else{
                ShowMessenge('Транзакция не создана', 'danger');
            }
            
            return Json::encode($result['error']);
        } catch (\DomainException $e) {
            Yii::$app->errorHandler->logException($e);
            ShowMessenge($e);
        }
    }
}