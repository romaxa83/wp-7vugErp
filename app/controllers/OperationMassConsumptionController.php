<?php

namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use app\controllers\AccessController;
use yii\filters\AccessControl;
use yii\helpers\Json;
use app\models\Agent;
use app\models\Operations;
use app\models\OperConsumption;
use app\models\Category;
use app\models\Product;
use app\models\VProduct;
use app\service\BazaApi;

class OperationMassConsumptionController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    AccessController::getAccessRules(Yii::$app->controller->id),
                    [
                        'actions' => ['show'],
                        'allow' => true,
                        'roles' => ['operation_update','operation_create']
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['operation_create'],
                    ],
                    [
                        'actions' => [
                            'first-step', 
                            'add-product', 
                            'delete-product', 
                            'ok-transaction'
                        ],
                        'allow' => true,
                        'roles' => ['operation_update'],
                    ]
                ]
            ]
        ];
    }
    /**
     * Метод отвечает отображения страници создания массовой транзакций , после отправки данных вызывает метод создания транзакций
     * @return mixed 
    */
    public function actionCreate()
    {
        $model = new Operations();
        $model->date = date("Y-m-d H:i:s");
        $shop = Agent::getAgentsMap(2);
        $repository = Agent::getAgentsMap(3);
        $post = Yii::$app->request->post();
        if(!empty($post)){
            $this->actionCreateTransaction($post);
        }
        return $this->render('create',[
            'model' => $model,
            'shop' => $shop,
            'repository' => $repository,
        ]);
    }
    /**
     * Метод отвечает за создания транзакий в случае ошибки перебросит на страницу создания и покажит ошибки
     * @return mixed 
    */
    private function actionCreateTransaction($post)
    {
        $oneTransaction = $post;
        $error = [];
        $id = [];
        foreach ($post['Operations']['where'] as $one){
            $model = new Operations();
            $oneTransaction['Operations']['where'] = $one;
            if($model->load($oneTransaction)){
                $model->transaction = '-A';
                $model->type = 2;
                $model->save();
                if($model->hasErrors()){
                    $error = $model->getErrorSummary(true);
                    ShowMessenge($model->getErrorSummary(true));
                }else{
                    $id[] = $model->id;

                    $transactionForApi[] = $model->getAttributes();
                    $course = $model['course'];
                }
            }
        }
        if(!empty($error)){
            return $this->redirect('create');
        }else {

            /*****___SEND_TO_API___ ******/
            $dataApi['requestData']['title'] = BazaApi::TRANSACTION_TITLE_CREATE_MASS_TRANSACTION;
            $dataApi['requestData']['body'] = $post['Operations'];
            $dataApi['requestData']['body']['course'] = $course;
            $dataApi['data'] = $transactionForApi;
            (new BazaApi('transaction','create'))->add($dataApi);

            return $this->redirect(['show','id' => $id]);
        }
    }
    /**
     * Отвечает за отображения страници наполнения транзакций 
     * @param array $id массив индефикаторов транзакций
     * @return mixed 
    */
    public function actionShow(array $id)
    {
        $product = new Product();
        $operConsumption = new OperConsumption();
        $model = Operations::findAll($id);
        $categories = Category::getListCategory();
        $shop = Agent::getAgentsMap(2);
        $repository = Agent::getAgentsMap(3);
        $type_price = Agent::find()->asArray()->select(['price_type'])->where(['id'=>$model[0]->where])->one();
        $OperConsumption = [];
        foreach($model as $one){
            $one = $one->getProducts()
            ->join('LEFT JOIN', 'product', 'product.id = oper_consumption.product_id')
            ->orderBy(['product.category_id' => SORT_ASC])->all();
            $OperConsumption = array_merge($OperConsumption,$one);
        }
        return $this->render('show',[
            'model' => $model,
            'categories' => $categories,
            'product' => $product,
            'operConsumption' => $operConsumption,
            'id' => $id[0],
            'type_price' => $type_price,
            'shop' => $shop,
            'repository' => $repository,
            'models' => $OperConsumption
        ]);
    }
    /**
     * Отвечает за рендер таблицы расприделения количества по транзакциям базового товара
     * @return mixed
    */
    public function actionFirstStep()
    {
        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $model = new OperConsumption();
            if(isset($post['OperConsumption'])){
                $product = Product::find()->select(['id','amount','name','price1','price2','is_variant'])->where(['id'=>$post['OperConsumption']['product_id']])->asArray()->one();
                $transaction = Operations::find()->where(['in','id',$post['id']])->with(['whereagent'])->asArray()->all();
                if($product['is_variant'] == 1){
                    $amount = OperConsumption::distributionAmount($product['amount'],$post['OperConsumption']['amount'],count($post['id']));
                    $view = $this->renderAjax('first-step',['product'=>$product,'amount'=>$amount,'transaction'=>$transaction,'model'=>$model]);
                }else{
                    $view = $this->FirstStepVariant($post,$transaction,$product);
                }
                return Json::encode(['status'=>'success','view' => $view]);
            }else{
                return Json::encode(['status'=>'error','msg'=> FormattedMessenge('Выберите товар')]);
            }
        }
    }
    /**
     * Отвечает за рендер таблицы расприделения количества по транзакциям вариативного товара
     * @return mixed
    */
    private function FirstStepVariant($post,$transaction,$product)
    {
        foreach($post['OperConsumptionVariant'] as $post){
            if($post['amount'] > 0){
                $id[] = $post['product_id'];
                $amount[] = $post['amount'];
            }
        }
        $vproduct = \app\models\VProduct::find()->select(['id','amount','price1','price2','char_value'])->where(['id'=>$id])->asArray()->all();
        $i = 0;
        foreach ($vproduct as $key => $one){
            $vproduct[$key]['char_value'] = VProduct::getCharValueFromId($one['char_value']);
            $amount[$i] = OperConsumption::distributionAmount($one['amount'],$amount[$i],count($transaction));
            $i++;
        }
        return $this->renderAjax('first-step-variant', compact('vproduct','transaction','product','amount','chars'));
    }
    /**
     * Отвечает за добавления базового/вариативного товара 
     * @return json
    */
    public function actionAddProduct()
    {
        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            foreach($post['data'] as $one){
                $model = new OperConsumption();
                $error = $model->SaveVariant(
                    [
                        'product_id' => isset($one['id_variant']) ? $one['id_variant'] : null,
                        'amount' => $one['amount'],
                        'price1' => $one['price']
                    ],
                    [
                        'product_id' => $one['id'],
                        'transaction_id' => $one['indexShop']
                    ]
                );
                $models[] = $model;
                $ids[] = $model->transaction_id;
                $ids['product'] = $model->product_id;
            }
            if(!empty($error)){
                $response = ['status'=>'error','msg'=> FormattedMessenge($error)];
            }else{

                /*****___SEND_TO_API___ ******/
                $dataApi['requestData']['title'] = BazaApi::TRANSACTION_TITLE_ADD_PRODUCT_MASS_TRANSACTION;
                $dataApi['requestData']['body'] = $post['data'];
                $dataApi['data']['transaction'] = Operations::find()->where(['in', 'id' ,$ids])->asArray()->all();
                $dataApi['data']['product'] = Product::find()->where(['id' => $ids['product']])->asArray()->one();
                (new BazaApi('transaction-product','create'))->add($dataApi);

                $response = ['status'=>'ok','html'=>$this->renderAjax('table-tr',compact('models')),'msg'=> FormattedMessenge($error)];
            }
            return JSON::encode($response);
        }
    }
    
    public static function actionDeleteProduct($data = null)
    {
        $post = ($data == null) ? Yii::$app->request->post() : $data;
        $product['base'] = Product::findOne($post['base']);
        $product['variant'] = \app\models\VProduct::findOne($post['variant']);
        $opertaion['transaction'] = Operations::findOne($post['transaction']);
        if(!empty($product['variant'])){
            $opertaion['rowTransaction'] = OperConsumption::find()->where(['transaction_id' => $post['transaction']])->andWhere(['product_id' => $post['base']])->andWhere(['vproduct_id' => $post['variant']])->one();
            $product['variant']->amount += $opertaion['rowTransaction']->amount;
            $product['variant']->update();
        }else{
            $opertaion['rowTransaction'] = OperConsumption::find()->where(['transaction_id' => $post['transaction']])->andWhere(['product_id' => $post['base']])->one();
        }
        $product['base']->amount += $opertaion['rowTransaction']->amount;
        $product['base']->update();
        $opertaion['rowTransaction']->delete();
        $opertaion['transaction']->SaveTotalValue();

        /****__SEND_TO_API__****/
        $dataApi['requestData']['title'] = BazaApi::TRANSACTION_TITLE_DELETE_MASS_TRANSACTION_PRODUCT;
        $dataApi['requestData']['body'] = $post;
        $dataApi['data']['transaction'] = $opertaion['transaction']->getAttributes();
        $dataApi['data']['product'] = $product['base']->getAttributes();
        (new BazaApi('transaction-product','delete'))->add($dataApi);

        return Json::encode(['status' => 'ok','total_price' => ['usd' => number_format($opertaion['transaction']->total_usd, getFloat('usd'),',',''),'ua' => number_format($opertaion['transaction']->total_ua, getFloat('usd'),',','')]]);
    }
    /**
     * Отвечает за формирования обьектов транзакций 
     * @return mixed
    */
    public function actionOkTransaction()
    {
        if(Yii::$app->request->isAjax){
            $id = Yii::$app->request->post();
            $models = Operations::findAll($id);
            foreach($models as $model){
                $transaction[] = $model->transaction;
                if(empty($model->products)){
                    $dataForApi['transaction'] = $model->transaction;
                    $dataForApi['type'] = $model->type;
                    
                    $model->delete();
                    unset($models);

                    /****__SEND_TO_API__****/
                    $dataApi['requestData']['body'] = $dataForApi;
                    $dataApi['data'] = '';
                    (new BazaApi('transaction','delete'))->add($dataApi);
                }else{
                    $model->status = 1;
                    $model->SaveTotalValue();
                    $model->update();
                    
                    $dataForApi[] = $model->getAttributes();
                }
            } 
            if(!empty($models)){
                /****__SEND_TO_API__****/
                $dataApi['requestData']['title'] = BazaApi::TRANSACTION_TITLE_CONFIRM_MASS_TRANSACTION;
                $dataApi['requestData']['body'] = $id;
                $dataApi['data'] = $dataForApi;
                (new BazaApi('transaction','update'))->add($dataApi);

                ShowMessenge(('Транзакций № ' . implode($transaction, ',') . ' сформированы'),'success');
            }else{
                ShowMessenge(('Иза отсутствия товаров, транзакций № ' . implode($transaction, ',') . ', не были сформированы'),'success');
            }
            return $this->redirect('/operation/all-consumption');
        }
    }
}