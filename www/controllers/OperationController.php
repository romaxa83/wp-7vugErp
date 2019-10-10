<?php

namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use app\models\Archive;
use app\models\ArchiveValue;
use app\models\Operations;
use app\models\OperationsSearch;
use app\models\Product;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use app\controllers\OperationComingController;
use app\controllers\OperationConsumptionController;
use app\controllers\OperationMassConsumptionController;
use yii\filters\AccessControl;
use app\controllers\AccessController;
use app\service\BazaApi;

class OperationController extends BaseController
{
    private $transaction;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    AccessController::getAccessRules(Yii::$app->controller->id),                    
                    [
                        'actions' => [
                            'all-transaction', 
                            'all-coming', 
                            'all-consumption',  
                            'get-transaction-table',  
                            'get-archive-table',
                            'archive'
                        ],
                        'allow' => true,
                        'roles' => ['operation_create','operation_update','operation_print']
                    ],
                    [
                        'actions' => [
                            'get-product-data', 
                            'get-vproduct-data',
                            'close-transaction', 
                            'cancel-transaction', 
                            'mass-archive', 
                            'send-in-archive', 
                            'send-in-archive-value'
                        ],
                        'allow' => true,
                        'roles' => ['operation_update']
                    ]
                ]
            ]
        ];
    }
    /**
     * Отвечает за рендер страницы с всеми транзакциями 
     * @return mixed
    */
    public function actionAllTransaction()
    {    
        $model = new OperationsSearch();
        $this->setFilterSession('operation', Yii::$app->getRequest()->getQueryParams());
        $dataProvider = $model->search(Yii::$app->session->get('operation_session_filter'));
        return $this->render('index', ['dataProvider' => $dataProvider,'type' => 'all','model' => $model]);
    }
    /**
     * Отвечает за рендер страницы с приходом
     * @return mixed
    */
    public function actionAllComing()
    {
        $query = Operations::find()->where(['type' => 1])->asArray()->with('whereagent')->with('whenceagent')->orderBy(['id'=>SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => getSizePage('operation') !== 0 ? getSizePage('operation') : 10,
            ],
        ]);
        return $this->render('index', ['dataProvider' => $dataProvider,'type' => 'coming']);
    }
    /**
     * Отвечает за рендер страницы с расходом
     * @return mixed
    */
    public function actionAllConsumption()
    {
        $query = Operations::find()->where(['type' => 2])->asArray()->with('whereagent')->with('whenceagent')->orderBy(['id'=>SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => getSizePage('operation') !== 0 ? getSizePage('operation') : 10,
            ],
        ]);
        return $this->render('index', ['dataProvider' => $dataProvider,'type' => 'consumption']);
    }
    /**
     * Отвечает за проверку присутствия базового товара в транзакций в случае отсутствия возрашает его атрибуты
     * @return json
    */
    public function actionGetProductData()
    {
        $data = Yii::$app->request->post();
        $this->transaction = Operations::findOne($data['transaction']);
        $product = Product::findOne($data['id']);
        $response['variant'] = false;
        if($product->is_variant == 2){
            $response = $this->GetVProductData($product,$data['type'],isset($data['typePrice']) ? $data['typePrice'] : null );
        }else {
            if($this->transaction->isProductExist($product->id)){
                $response['status'] = 'exist';
            }else{
                $response['status'] = 'ok';
                $response['product'] = $product; 
            }
        }
        return JSON::encode($response);
    }
    /**
     * Отвечает за проверку присутствия вариаций товара в транзакций в случае отсутствия возрашает его атрибуты
     * @return array $response 
    */
    private function GetVProductData($product,$type,$typePrice = null)
    {
        $response['product'] = $product;
        $response['status'] = 'ok';
        $response['variant'] = true;
        if($this->transaction->isProductExist($product->id)){
            $v_products = $this->transaction->getNotExistVProducts($product->id);
        }else{
            $v_products = $product->vproducts;
        }
        if(empty($v_products)){
            $response['status'] = 'exist';
        }else{
            $response['status'] = 'ok';
            $response['html'] = $this->renderPartial('../operation-'.$type.'/_variant_table', [
                'product' => $product,
                'v_products' => $v_products,
                'char_values' => Operations::getNameCharsAndValue($v_products),
                'typePrice' => $typePrice
            ]);
        }
        return $response;
    }
    /**
     * Отвечает за закрытия транзакций на редактирования 
     * @return mixed
    */
    public function actionCloseTransaction()
    {
        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $transaction = Operations::findOne($post['trans_id']);
            $transaction->status = 2;
            if($transaction->update()){

                /****__SEND_TO_API__****/
                $dataApi['requestData']['title'] = BazaApi::TRANSACTION_TITLE_STOP_EDIT;
                $dataApi['requestData']['body'] = $post['trans_id'];
                $dataApi['data'] = $transaction->getAttributes();
                (new BazaApi('transaction','update'))->add($dataApi);
                
                ShowMessenge('Транзакция № '.$transaction->transaction . ' Успешна закрыта','success');
            }else{
                ShowMessenge('Произошла ошибка');
            }
            return $this->redirect(Yii::$app->request->referrer);
        }
    }
    
    public function actionCancelTransaction()
    {
        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            if($post['type'] == 'coming' || $post['type'] == 'consumption'){
                $model = Operations::findOne($post['id']);
                $product = $model->products;
                foreach ($product as $one){
                    if($post['type'] == 'coming'){
                        OperationComingController::actionDeleteProduct(['base' => $one->product_id,'variant' => $one->vproduct_id,'transaction' => $one->transaction_id]);
                    }else{
                        OperationConsumptionController::actionDeleteProduct(['base' => $one->product_id,'variant' => $one->vproduct_id,'transaction' => $one->transaction_id]);
                    }
                }
                $dataForApi ['transaction'] = $model->transaction;
                $dataForApi ['type'] = $model->type;

                $model->delete();

                /****__SEND_TO_API__****/
                $dataApi['requestData']['body'] = $dataForApi;
                $dataApi['data'] = '';
                (new BazaApi('transaction','delete'))->add($dataApi);

                ShowMessenge('Транзакция № '.$model->transaction . ' Успешна отменена','success');
                return $this->redirect('/operation/all-'.$post['type']);
            }
            if($post['type'] == 'mass-consumption'){
                $post['id'] = explode(',', $post['id']);
                $this->CancelMassConsumption($post);
                return $this->redirect('/operation/all-consumption');
            }
        }
    }
    
    private function CancelMassConsumption($post)
    {
        $model = Operations::find()->where(['in','id',$post['id']])->all();
        $transaction = '';
        foreach($model as $oneTransaction){
            foreach($oneTransaction->products as $one){
                OperationMassConsumptionController::actionDeleteProduct(['base' => $one->product_id,'variant' => $one->vproduct_id,'transaction' => $one->transaction_id]);
            }
            $transaction .= $oneTransaction->transaction;
            $trans[] = $oneTransaction->transaction;
            $oneTransaction->delete();
        }

        /*****___SEND_TO_API___ ******/
        $dataApi1['requestData']['title'] = BazaApi::TRANSACTION_TITLE_DELETE_MASS_TRANSACTION;
        $dataApi1['requestData']['body'] = $trans;
        $dataApi1['data']['transaction'] = '';
        (new BazaApi('transaction','delete'))->add($dataApi1);

        ShowMessenge('Транзакция № '.$transaction . ' Успешна отменены','success');
    }
    /**
     * Отвечает за рендер таблици с информацией о транзакций 
     * @return mixed 
    */
    public function actionGetTransactionTable()
    {
        if(Yii::$app->request->isAjax){
            $trans_id = Yii::$app->request->post('trans_id');
            $transaction = Operations::findOne($trans_id);
            $query = $transaction->getProducts()
                ->join('LEFT JOIN', 'product', 'product.id = product_id')
                ->orderBy([
                    'product.category_id' => SORT_ASC,
                    'product.name' => SORT_ASC,
                    ]);
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => false,
                ],
            ]);
            $print_price = [
                'start_price' => ($transaction->type == 1 || $transaction->type == 3) ? true : false,
                'trade_price' => ($transaction->type == 2 ||$transaction->type == 3) ? true : false,
                'price' => ($transaction->type == 2) ? true : false
            ];
            return Json::encode([
                'view' => $this->renderPartial('table-transaction',[
                    'dataProvider' => $dataProvider,
                    'transaction' => $transaction,
                    'print_price' => $print_price
                ]),
                'empty' => count($dataProvider->getModels()) > 0 ? false : true
            ]);
        }
    }

    public function actionMassArchive()
    {
        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $transaction = '';
            foreach ($post['transactionId'] as $key => $value) {
                $archive_id = JSON::decode($this->actionSendInArchive(['id' => $value]));
                $this->actionSendInArchiveValue(['id' => $archive_id],true);
                $transaction .= $archive_id['transaction'] . ' ,';
            }
            ShowMessenge('Транзакция № ' . $transaction . ' Успешно отправлена в архив','success');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }
    
    public function actionArchive()
    {
        $query = Archive::find()->asArray()->with('where')->with('whence')->orderBy(['id'=>SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => getSizePage('operation') !== 0 ? getSizePage('operation') : 10,
            ],
        ]);
        return $this->render('archive', ['dataProvider' => $dataProvider,'type' => 'coming']);
    }

    public function actionSendInArchive($data = null)
    {
        $post = ($data == null) ? Yii::$app->request->post() : $data;
        $model = Operations::findOne($post['id']);
        $modelArchive = new \app\models\Archive();
        return JSON::encode($modelArchive->LoadModel($model));
    }
    
    public function actionSendInArchiveValue($data = null,$mass = false)
    {
        $post = ($data == null) ? Yii::$app->request->post() : $data;
        $model = Operations::findOne($post['id']['transaction_id']);
        foreach($model->products as $one){
            $modelArchiveValue = new ArchiveValue();
            $modelArchiveValue->LoadProduct($one,$post['id']['archive_id']);
            $one->delete();
        }
        $transaction = $model->transaction;
        $model->delete();
        if($mass === false) {
            ShowMessenge('Транзакция № ' . $transaction . ' Успешно отправлена в архив','success');
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionGetArchiveTable()
    {
        if(Yii::$app->request->isAjax){
            $archive_id = Yii::$app->request->post('trans_id');
            $archive = Archive::findOne($archive_id);
            $query = $archive->getProducts()
                ->join('LEFT JOIN', 'product', 'product.id = product_id')
                ->orderBy(['product.category_id' => SORT_ASC]);
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => getSizePage('operation') !== 0 ? getSizePage('operation') : 10,
                ],
            ]);
            $print_price = [
                'start_price' => ($archive->type == 1 || $archive->type == 3) ? true : false,
                'trade_price' => ($archive->type == 2 ||$archive->type == 3) ? true : false,
                'price' => ($archive->type == 2) ? true : false
            ];
            return Json::encode([
                'view' => $this->renderPartial('table-transaction',[
                    'dataProvider' => $dataProvider,
                    'transaction' => $archive,
                    'print_price' => $print_price
                ]),
                'empty' => count($dataProvider->getModels()) > 0 ? false : true
            ]);
        }
    }
}