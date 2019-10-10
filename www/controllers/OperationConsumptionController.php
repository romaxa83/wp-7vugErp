<?php

namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use app\controllers\AccessController;
use app\models\Agent;
use app\models\Operations;
use app\models\Category;
use app\models\Product;
use app\models\OperConsumption;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;
use app\service\BazaApi;

class OperationConsumptionController extends BaseController 
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    AccessController::getAccessRules(Yii::$app->controller->id),                    
                    [
                        'actions' => ['show','update'],
                        'allow' => true,
                        'roles' => ['operation_create','operation_update'],
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['operation_create'],
                    ],
                    [
                        'actions' => ['add-product', 'add-vproduct', 'delete-product'],
                        'allow' => true,
                        'roles' => ['operation_update'],
                    ],
                    [
                        'actions' => ['print-pdf'],
                        'allow' => true,
                        'roles' => ['operation_print'],
                    ]
                ],
            ]
        ];
    }
    /**
     * Метод отвечает за создания тела транзакций в случай удачи перекидывает на страницу отображения транзакций 
     * @return mixed 
    */
    public function actionCreate()
    {
        $model = new Operations();
        $model->date = date("Y-m-d H:i:s");
        $post = Yii::$app->request->post();
        if($post && $model->load($post)){
            $model->type = 2;
            $model->save();
            if($model->hasErrors()){
                ShowMessenge($model->getErrorSummary(true));
                return $this->redirect('create');
            }else {

                /****__SEND_TO_API__****/
                $dataApi['requestData']['title'] = BazaApi::TRANSACTION_TITLE_CONSUMPTION;
                $dataApi['requestData']['body']  = $post['Operations'];
                $dataApi['requestData']['body']['whence']  = $model->whence;
                $dataApi['requestData']['body']['course'] = $model->course;
                $dataApi['data'] = $model->getAttributes();
                (new BazaApi('transaction','create'))->add($dataApi);

                return $this->redirect(['show','id' => $model->id]);
            }
        }else {
            $shop = Agent::getAgentsMap(2);
            $repository = Agent::getAgentsMap(3);
            $query = Operations::find()->select(['id','transaction', 'where','type','status'])->where(['status' => 0,'type' => 2])->with('whereagent')->asArray()->orderBy(['id'=>SORT_DESC]);
            $lost_transaction = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => getSizePage('operation') !== 0 ? getSizePage('operation') : 10,
                ],
            ]);
            return $this->render('create',[
                'model' => $model,
                'shop' => $shop,
                'repository' => $repository,
                'lost_transaction' => $lost_transaction
            ]);
        }
    }
    /**
     * Отвечает за формирования/сохранения обьекта транзакций 
     * @return mixed
    */
    public function actionUpdate($id)
    {
        $model = Operations::findOne($id);
        $post = Yii::$app->request->post();
        $lastShop = $model->where;
        if($model->load($post)){
            empty($model->where) ? $model->where = $lastShop : null;
            $model->save();
            if(empty($model->products)){
                $dataForApi ['transaction'] = $model->transaction;
                $dataForApi ['type'] = $model->type;

                $model->delete();

                /****__SEND_TO_API__****/
                $dataApi['requestData']['body'] = $dataForApi;
                $dataApi['data'] = '';
                (new BazaApi('transaction','delete'))->add($dataApi);

                ShowMessenge('Транзакция № '.$model->transaction.' сохранена пустой, по этому была удалена','success');
                return $this->redirect('/operation/all-consumption');
            }else{
                if($model->hasErrors()){
                    ShowMessenge($model->getErrorSummary(true));
                    return $this->redirect('/operation-consumption/show?id='.$model->id);
                } else{
                    $model->status = 1;
                    $model->update();

                    /****__SEND_TO_API__****/
                    $dataApi['requestData']['title'] = BazaApi::TRANSACTION_TITLE_CONFIRM;
                    $dataApi['requestData']['body'] = $post['Operations'];
                    $dataApi['data'] = $model->getAttributes();
                    (new BazaApi('transaction','update'))->add($dataApi);
                    
                    ShowMessenge('Транзакция № '.$model->transaction.' успешно сохранена','success');
                    return $this->redirect('/operation/all-consumption');
                }
            }
        }
    }
    /**
     * Отвечает за отображения страници наполнения транзакций 
     * @param integer $id Индефикатор транзакций
     * @return mixed
    */
    public function actionShow($id)
    {
        $model = Operations::find()->where(['id' => $id])->with('whereagent')->one();
        $shop = Agent::getAgentsMap(2);
        $repository = Agent::getAgentsMap(3);
        $categories = Category::getListCategory();
        $product = new Product();
        $operConsumption = new OperConsumption();
        $type_price = Agent::find()->asArray()->select(['price_type'])->where(['id' => $model->where])->one();
        $OperConsumption = $model->getProducts()
            ->join('LEFT JOIN', 'product', 'product.id = oper_consumption.product_id')
            ->orderBy(['product.category_id' => SORT_ASC])->all();
        return $this->render('show',[
            'model' => $model,
            'shop' => $shop,
            'repository' => $repository,
            'categories' => $categories,
            'product' => $product,
            'operConsumption' => $operConsumption,
            'type_price' => $type_price,
            'OperConsumption' => $OperConsumption
        ]);
    }
    /**
     * Отвечает за добавления базового товара 
     * @return json
    */
    public function actionAddProduct()
    {
        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $model = new OperConsumption();
            if($model->load($post) || !$model->validate()){
                $model->save();
                if($model->hasErrors()){
                    return JSON::encode([
                        'type'  => 'error',
                        'msg' => FormattedMessenge($model->getErrorSummary(true))
                    ]);
                }else {

                    /****__SEND_TO_API__****/
                    $dataApi['requestData']['title'] = BazaApi::TRANSACTION_TITLE_ADD_PRODUCT_CONSUMPTION_TRANSACTION;
                    $dataApi['requestData']['body'] = $post['OperConsumption'];
                    $dataApi['data']['transaction'] = (Operations::find()->where(['id' => $post['OperConsumption']['transaction_id']])->one())->getAttributes();
                    $dataApi['data']['product'] = $model->getProduct()->asArray()->one();
                    (new BazaApi('transaction-product','create'))->add($dataApi);

                    return JSON::encode(['view' => $this->renderAjax('table-tr',[
                        'OperConsumption'=> $model,
                        'index' =>  count($model->transaction->products)
                    ]),
                        'msg' => FormattedMessenge(false),
                        'total_price' =>
                            [
                                'total_uah' => number_format($model->transaction->total_ua, getFloat('uah'),',',''),
                                'total_usd' => number_format($model->transaction->total_usd, getFloat('usd'),',','')
                            ]
                    ]);
                }
            }
        }
    }
    /**
     * Отвечает за добавления вариативного товара 
     * @return json
    */
    public function actionAddVProduct()
    {
        if (Yii::$app->request->isAjax) {
            $view = "";
            $post = Yii::$app->request->post();
            $total_price = false;
            $error = false;
            foreach ($post['OperConsumptionVariant'] as $variant){
                if($variant['amount'] === 0 || empty($variant['amount'])){ continue; }
                $model = new OperConsumption();
                $model->SaveVariant($variant,$post['OperConsumption']);
                if(!$model->hasErrors() && isset($model->id)){
                    $view .= $this->renderAjax('table-tr',[
                        'OperConsumption'=> $model,
                        'index' =>  count($model->transaction->products)
                    ]);
                }else{
                    $error[] = $model->getErrorSummary(true);
                }
            }
            if(isset($model)){
                $total_price['total_uah'] = number_format($model->transaction->total_ua, getFloat('uah'),',','');
                $total_price['total_usd'] = number_format($model->transaction->total_usd, getFloat('usd'),',','');
            }else{
                 $error[] = 'Значение «Кол-во» должно быть не меньше 1';
            }
            return JSON::encode(['view' => $view, 'msg' => FormattedMessenge($error),'total_price' =>  $total_price]);
        }
    }
    
    public static function actionDeleteProduct($data = null)
    {
        $post = ($data == null) ? Yii::$app->request->post() : $data;
        $product['base'] = Product::findOne($post['base']);
        $product['variant'] = \app\models\VProduct::findOne($post['variant']);
        $opertaion['transaction'] = Operations::findOne($post['transaction']);
        $recount = isset($product->date_adjustment) ? ($opertaion['transaction']->date > $product->date_adjustment) : true;
        if(!empty($product['variant'])){
            $opertaion['rowTransaction'] = OperConsumption::find()->where(['transaction_id' => $post['transaction']])->andWhere(['product_id' => $post['base']])->andWhere(['vproduct_id' => $post['variant']])->one();
            $product['variant']->amount += $opertaion['rowTransaction']->amount;
            ($recount) ? $product['variant']->update() : null;
        }else{
            $opertaion['rowTransaction'] = OperConsumption::find()->where(['transaction_id' => $post['transaction']])->andWhere(['product_id' => $post['base']])->one();
        }
        $product['base']->amount += $opertaion['rowTransaction']->amount;
        ($recount) ? $product['base']->update() : null;
        $opertaion['rowTransaction']->delete();
        $opertaion['transaction']->SaveTotalValue();

        /****__SEND_TO_API__****/
        $dataApi['requestData']['title'] = BazaApi::TRANSACTION_TITLE_DELETE_PRODUCT_CONSUMPTION;
        $dataApi['requestData']['body'] = $post;
        $dataApi['data']['transaction'] = $opertaion['transaction']->getAttributes();
        $dataApi['data']['product'] = $product['base']->getAttributes();
        (new BazaApi('transaction-product','delete'))->add($dataApi);

        return Json::encode(['status' => 'ok','total_price' => ['usd' => number_format($opertaion['transaction']->total_usd, getFloat('usd'),',',''),'ua' => number_format($opertaion['transaction']->total_ua, getFloat('usd'),',','')]]);
    }
    /**
     * Отвечает за печать транзакий 
     * @param integer $id Индефикатор транзакций
     * @return mixed
    */
    public function actionPrintPdf($id,$type = 'default')
    {
        if (Yii::$app->user->can('operation_print')){
            $model = new Operations();
            $view = $this->getViewForPdf($id,$type);
            $pdf = $model->getPdf($view);
            return $pdf->render();
        }else{
            throw new ForbiddenHttpException('У вас нет прав для этих действий');
        }
    }
    /**
     * Отвечает за рендер вида печать транзакций
     * @param integer $id Индефикатор транзакций
     * @return mixed
    */
    protected function getViewForPdf($id,$type)
    {
        $operation = Operations::findOne($id);
        $product = $operation->getProducts()
            ->join('LEFT JOIN', 'product', 'product.id = product_id')
            ->orderBy([
                'product.category_id' => SORT_ASC,
                'product.name' => SORT_ASC,
            ])
            ->all();

        $shop = Agent::getAgentsMap(2);
        $repository = Agent::getAgentsMap(3);
        return $this->renderPartial('pdf',compact('operation','product','shop','repository','type'));
    }
}
