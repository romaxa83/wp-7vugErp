<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use app\models\Operations;
use app\models\Agent;
use app\models\Category;
use app\models\OperComing;
use app\models\Product;
use app\models\VProduct;
use yii\helpers\Json;
use yii\web\ForbiddenHttpException;
use app\controllers\AccessController;
use app\service\BazaApi;


class OperationComingController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [ 
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    AccessController::getAccessRules(Yii::$app->controller->id),
                    [
                        'actions' => ['show', 'update'],
                        'allow' => true,
                        'roles' => ['operation_create', 'operation_update']
                    ],
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['operation_create']
                    ],
                    [
                        'actions' => ['add-product', 'add-vproduct', 'delete-product'],
                        'allow' => true,
                        'roles' => ['operation_update']
                    ],
                    [
                        'actions' => ['print-pdf'],
                        'allow' => true,
                        'roles' => ['operation_print']
                    ]
                ]
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
            $model->type = 1;
            $model->save();
            if($model->hasErrors()){
                ShowMessenge($model->getErrorSummary(true));
                return $this->redirect('create');
            } else{

                /****__SEND_TO_API__****/
                $dataApi['requestData']['title'] = BazaApi::TRANSACTION_TITLE_COMING;
                $dataApi['requestData']['body']  = $post['Operations'];
                $dataApi['requestData']['body']['whence']  = $model->whence;
                $dataApi['requestData']['body']['course'] = $model->course;
                $dataApi['data'] = $model->getAttributes();
                (new BazaApi('transaction','create'))->add($dataApi);

                return $this->redirect(['show','id' => $model->id]);
            }
        }else {
            $agents = Agent::getAgentsMap(1);
            $repository = Agent::getAgentsMap(3);
            $query = Operations::find()->select(['id','transaction', 'whence','type','status'])->where(['status' => 0,'type' => 1])->with('whenceagent')->asArray()->orderBy(['id'=>SORT_DESC]);
            $lost_transaction = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => getSizePage('operation') !== 0 ? getSizePage('operation') : 10,
                ],
            ]);
            return $this->render('create',[
                'model' => $model,
                'agents' => $agents,
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
        $lastAgent = $model->whence;
        if($model->load($post)){
            empty($model->whence) ? $model->whence = $lastAgent : null;
            $model->save();
            if(empty($model->products)){
                $dataForApi ['transaction'] = $model->transaction;
                $dataForApi ['type'] = $model->type;

                $model->delete();

                //****__SEND_TO_API__****/
                $dataApi['requestData']['body'] = $dataForApi;
                $dataApi['data'] = '';
                (new BazaApi('transaction','delete'))->add($dataApi);

                ShowMessenge('Транзакция № '.$model->transaction.' сохранена пустой, по этому была удалена','success');
                return $this->redirect('/operation/all-coming');
            }else{
                if($model->hasErrors()){
                    ShowMessenge($model->getErrorSummary(true));
                    return $this->redirect('/operation-coming/show?id='.$model->id);
                } else{
                    $model->status = 1;
                    $model->update();

                    /****__SEND_TO_API__****/
                    $dataApi['requestData']['title'] = BazaApi::TRANSACTION_TITLE_CONFIRM;
                    $dataApi['requestData']['body'] = $post['Operations'];
                    $dataApi['data'] = $model->getAttributes();
                    (new BazaApi('transaction','update'))->add($dataApi);

                    ShowMessenge('Транзакция № '.$model->transaction.' успешно сохранена','success');
                    return $this->redirect('/operation/all-coming');
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
        $transaction = Operations::find()->where(['id' => $id])->with('whenceagent')->one();
        $oper_coming = new OperComing();
        $agents = Agent::getAgentsMap(1);
        $repository = Agent::getAgentsMap(3);
        $categories = Category::getListCategory();
        $coming_products = $transaction->getProducts()
            ->join('LEFT JOIN', 'product', 'product.id = product_id')
            ->orderBy(['product.category_id' => SORT_ASC])->all();
        return $this->render('show',[
            'model' => $transaction,
            'agents' => $agents,
            'repository' => $repository,
            'oper_coming' => $oper_coming,
            'categories' => $categories,
            'coming_products' => $coming_products
        ]);
    }
    /**
     * Отвечает за добавления базового товара 
     * @return json
    */
    public function actionAddProduct()
    {
        if (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();

            $model = new OperComing();
            if($model->load($post) || !$model->validate()){
                $model->save();
                if($model->hasErrors()){
                    return JSON::encode([
                        'type'  => 'error',
                        'msg' => FormattedMessenge($model->getErrorSummary(true))
                    ]);
                }else {

                    /****__SEND_TO_API__****/
                    $dataApi['requestData']['title'] = BazaApi::TRANSACTION_TITLE_ADD_PRODUCT_COMING_TRANSACTION;
                    $dataApi['requestData']['body'] = $post['OperComing'];
                    $dataApi['data']['transaction'] = (Operations::find()->where(['id' => $post['OperComing']['transaction_id']])->one())->getAttributes();
                    $dataApi['data']['product'] = $model->getProduct()->asArray()->one();
                    (new BazaApi('transaction-product','create'))->add($dataApi);

                    return JSON::encode(['view' => $this->renderAjax('table-tr',[
                        'coming_product'=> $model,
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
            foreach ($post['OperComingVariant'] as $variant){
                if($variant['amount'] === 0 || empty($variant['amount'])){ continue; }
                $model = new OperComing();
                $model->SaveVariant($variant,$post['OperComing']);
                if(!$model->hasErrors() && isset($model->id)){
                    $view .= $this->renderAjax('table-tr',[
                        'coming_product'=> $model,
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
        if(!empty($post['variant'])){
            return JSON::encode(self::actionDeleteVProduct($post));
        }else{
            $operation['transaction'] = Operations::find()->where(['>=','operations.id',$post['transaction']])->leftJoin('oper_coming', 'oper_coming.transaction_id = operations.id')->andWhere(['oper_coming.product_id' => $post['base']])->limit(2)->all(); 
            $operation['rowTransaction'] = OperComing::find()->where(['>=','transaction_id',$post['transaction']])->andWhere(['product_id' => $post['base']])->limit(2)->all();
            $product['base'] = Product::findOne($post['base']);
            $recount = isset($product['base']->date_adjustment) ? ($operation['transaction'][0]->date > $product['base']->date_adjustment) : true;
            if(count($operation['transaction']) > 1){
                $operation['rowTransaction'][1]->old_amount = $operation['rowTransaction'][0]->old_amount;
                $operation['rowTransaction'][1]->old_cost_price = $operation['rowTransaction'][0]->old_cost_price;
                $product['base']->start_price = $operation['rowTransaction'][0]->start_price;
                ($recount) ? $operation['rowTransaction'][1]->update() : null;
                ($recount) ? $operation['transaction'][1]->updateProductChain($product,['mark' => 'minus','value' => $operation['rowTransaction'][0]->amount]) : null;
            }else{
                $product['base']->cost_price = $operation['rowTransaction'][0]->old_cost_price; 
            }
            $product['base']->start_price = $product['base']->getLastStartPrice();
            $product['base']->trade_price = getTradePrice($product['base']['cost_price']);
            $product['base']->amount -= $operation['rowTransaction'][0]->amount;
            ($recount) ? $product['base']->update() : null;
            $operation['rowTransaction'][0]->delete();
            $operation['transaction'][0]->SaveTotalValue();

            /****__SEND_TO_API__****/
            $dataApi['requestData']['title'] = BazaApi::TRANSACTION_TITLE_DELETE_PRODUCT_COMING;
            $dataApi['requestData']['body'] = $post;
            $dataApi['data']['transaction'] = $operation['transaction'][0]->getAttributes();
            $dataApi['data']['product'] = $product['base']->getAttributes();
            (new BazaApi('transaction-product','delete'))->add($dataApi);

            return JSON::encode(['status' => 'ok','total_price' => ['usd' => number_format($operation['transaction'][0]->total_usd, getFloat('usd'),',',''),'ua' => number_format($operation['transaction'][0]->total_ua, getFloat('uah'),',','')]]);
        }
    }

    private function actionDeleteVProduct($post)
    {
        $product['variant'] = VProduct::find()->where(['product_id' => $post['base']])->andWhere(['id' => $post['variant']])->one();
        $product['base'] = Product::findOne($post['base']);
        $operation['rowTransaction'] = Operations::ArrayBolting(OperComing::find()->where(['transaction_id' => $post['transaction']])->andWhere(['product_id' => $post['base']])->all(),$product);
        if(count($operation['rowTransaction']) < 2){
            $operation['rowTransaction'][1] = OperComing::find()->where(['>','transaction_id',$post['transaction']])->andWhere(['product_id' => $post['base']])->one();
        }
        if(empty($operation['rowTransaction'][1])) { 
            unset($operation['rowTransaction'][1]); 
            $operation['transaction'] = Operations::find()->where(['in','id',[$operation['rowTransaction'][0]->transaction_id]])->all();
        }else{
            $operation['transaction'] = Operations::find()->where(['in','id',[$operation['rowTransaction'][0]->transaction_id,$operation['rowTransaction'][1]->transaction_id]])->all();
        }
        $recount = isset($product['base']->date_adjustment) ? ($operation['transaction'][0]->date > $product['base']->date_adjustment) : true;
        if(count($operation['transaction']) > 1){
            $operation['rowTransaction'][1]->old_amount = $operation['rowTransaction'][0]->old_amount;
            $operation['rowTransaction'][1]->old_cost_price = $operation['rowTransaction'][0]->old_cost_price;
            ($recount) ? $operation['rowTransaction'][1]->update() : null;  
            ($recount) ? $operation['transaction'][1]->updateVproductChain($product,['mark' => 'minus','value' => $operation['rowTransaction'][0]->amount]) : null;
        }else{
            $operation['rowTransaction'] = Operations::ArrayBolting(OperComing::find()->where(['transaction_id' => $post['transaction']])->andWhere(['product_id' => $product['base']->id])->all(),$product);
            if(count($operation['rowTransaction']) > 1){
                $operation['rowTransaction'][1]->old_amount = $operation['rowTransaction'][0]->old_amount;
                $operation['rowTransaction'][1]->old_cost_price = $operation['rowTransaction'][0]->old_cost_price;
                ($recount) ? $operation['rowTransaction'][1]->update() : null;
                ($recount) ? $operation['transaction'][0]->updateVproductChain($product,['mark' => 'minus','value' => $operation['rowTransaction'][0]->amount]) : null;
            }else{
                $product['base']->cost_price = $operation['rowTransaction'][0]->old_cost_price; 
            }
        }
        $product['base']->amount -= $operation['rowTransaction'][0]->amount;
        $product['variant']->amount -= $operation['rowTransaction'][0]->amount;
        ($recount) ? $product['base']->update() : null; 
        ($recount) ? $product['variant']->update() : null;
        $operation['rowTransaction'][0]->delete();
        $operation['transaction'][0]->SaveTotalValue();
        return ['status' => 'ok','total_price' => ['usd' => number_format($operation['transaction'][0]->total_usd, getFloat('usd'),',',''),'ua' => number_format($operation['transaction'][0]->total_ua, getFloat('uah'),',','')]];
    }
    /**
     * Отвечает за печать транзакий 
     * @param integer $id Индефикатор транзакций
     * @throws ForbiddenHttpException
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
            ])->all();
        $agents = Agent::getAgentsMap(1);
        $repository = Agent::getAgentsMap(3);
        return $this->renderPartial('pdf',compact('operation','product','agents','repository','type'));
    }
}