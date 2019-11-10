<?php
namespace app\controllers;

use Yii;
use app\models\Product;
use app\models\VProduct;
use app\models\Operations;
use app\models\OperComing;
use app\models\OperConsumption;
use yii\helpers\Json;
use yii\filters\AccessControl;
use app\controllers\AccessController;
use app\modules\logger\service\LogService;
use app\service\BazaApi;

class LiveEditController extends BaseController 
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
                        'actions' => ['entry'],
                        'roles' => ['operation_update'],
                    ],
                ],
            ],
        ];
    }
    //производить расчеты или нет 
    public $recount;
    
    public function sync($data)
    {
        $test = new LiveEditController('live/edit',\yii\base\Module::className());
        return $test->actionEntry($data);
    }
    //вход, распределения по методам 
    public function actionEntry($data = null)
    {
        $post = ($data == null) ? Yii::$app->request->post() : $data;
        
        $dataForApi = $post;

        if($post['typeLifeEdit'] === 'edit-vproduct-catalog'){
            $answer = $this->ChangeVproductFromCatalog($post);
        }
        if($post['typeLifeEdit'] === 'edit-coming'){
            $answer = $this->ChangeProductComing($post);
        }
        if($post['typeLifeEdit'] === 'edit-consumption'){
            $answer = $this->ChangeProductConsumption($post);
        }

        /*****___SEND_TO_API___ ******/
        $dataApi['requestData']['title'] = BazaApi::TRANSACTION_TITLE_LIVE_EDIT;
        $dataApi['requestData']['body'] = $dataForApi;
        $dataApi['data']['transaction'] = (Operations::find()->where(['id' => $post['transaction_id']])->one())->getAttributes();
        $dataApi['data']['product'] = (Product::find()->where(['id' => $post['productId']])->one())->getAttributes();
        (new BazaApi('transaction-product','update'))->add($dataApi);

        return $answer;
    }
    //логика для ред. вариативного с каталога 
    private function ChangeVproductFromCatalog($data)
    {
        $vproduct = VProduct::find()->where(['id' => $data['variantId']])->andWhere(['product_id' => $data['productId']])->one();
        $answer = ['status' => 'ok','value' => $data['value']];
        if($data['field'] === 'price1' || $data['field'] === 'price2'){
            $vproduct->{$data['field']} = $data['value'];
        }
        if($data['field'] === 'amount'){
            $summVproduct = VProduct::find()->where(['product_id' => $data['productId']])->andWhere(['!=','id',$data['variantId']])->sum('amount');
            $baseAmount = Product::find()->select(['amount'])->asArray()->where(['id' => $data['productId']])->one();
            $balance = $baseAmount['amount'] - $summVproduct;
            $difference = $balance - $data['value'];
            if($difference != 0 && $difference > 0){
                $vproduct->amount = $data['value'];
            }else{
                $vproduct->amount = $balance;
                $data['value'] = $vproduct->amount;
                $answer = ['status' => 'error','text' => 'Вы попытались ввести значения больше допустимого','value' => $data['value']];
            }
        }
        $vproduct->update();
        return Json::encode($answer);
    }
    //логика для ред. базового товара прихода 
    private function ChangeProductComing($data)
    {
        //заносив в переменые данные продутка 
        $product = Product::findOne($data['productId']);
        $vproduct = VProduct::findOne($data['variantId']);
        //заносив в переменые данные транзакций и строчки транзакций
        $operation = Operations::findOne($data['transaction_id']);
        $operComing = OperComing::find()->where(['product_id' => $data['productId']])->andWhere(['vproduct_id' => empty($data['variantId']) ? null : $data['variantId']])->andWhere(['transaction_id' => $data['transaction_id']])->one(); 
        //изменения заноситься в продукт ?
        $this->recount = isset($product->date_adjustment) ? ($operation->date > $product->date_adjustment) : true;
        if($data['field'] === 'amount'){
            return $this->changeAmountComing($data,['base' => $product,'variant' => $vproduct],['transaction' => $operation,'rowTransaction' => $operComing]);
        }
        if($data['field'] === 'start_price' || $data['field'] === 'start_price_ua'){
            return $this->changeStartPriceComing($data,['base' => $product,'variant' => $vproduct],['transaction' => $operation,'rowTransaction' => $operComing]);
        }
        if($data['field'] === 'price1' || $data['field'] === 'price2'){
            return $this->changePriceComing($data,['base' => $product,'variant' => $vproduct],['transaction' => $operation,'rowTransaction' => $operComing]);
        }
    }
    //логика для ред. количества товара прихода 
    private function changeAmountComing($data,$product,$operation)
    {
        $action = true;
        $difference = $this->CheckDifferenceAmount($operation, $data);
        //если недостаточно откидуем действие но если в след транзакциях нет корректировки 
        if($difference['mark'] !== 'minus' || $product['base']->amount >= ($operation['rowTransaction']->amount - $data['value']) || $this->recount == false){
            if($data['value'] == 0){
                ($this->recount) ? $action = $operation['transaction']->AmountChangeToZeroRecount($product) : null;
            }else{
                //обновляем значения oper_coming
                $operation['rowTransaction']->amount = $data['value'];                       
                $operation['rowTransaction']->update();
                LogService::logModel($operation['rowTransaction'], 'update');         
                //обновляем значения product
                $product['base']->amount = ($difference['mark'] === 'plus') ? $product['base']->amount + $difference['value'] : $product['base']->amount - $difference['value'];                
                ($this->recount) ? $product['base']->update() : null;
                LogService::logModel($product['base'], 'update');
                //считаем цепочку и заносим конечное значения 
                if(empty($product['variant'])){
                    ($this->recount) ? $action = $operation['transaction']->updateProductChain($product,$difference) : null;
                }else{
                    $product['variant']->amount = ($difference['mark'] === 'plus') ? $product['variant']->amount + $difference['value'] : $product['variant']->amount - $difference['value'];
                    ($this->recount) ? $product['variant']->update() : null;                
                    ($this->recount) ? $action = $operation['transaction']->updateVproductChain($product,$difference) : null;
                } 
            }  
            $operation['transaction']->SaveTotalValue();
            $answer = ['status' => 'ok','value' => $data['value'],'total_price' => ['total_usd' => formatedPriceUSD($operation['transaction']->total_usd),'total_ua' => formatedPriceUSD($operation['transaction']->total_ua)]];
        }else{
            $answer = ['status' => 'error','text' => 'Вы уменьшили на количество отсутствующие на складе . Текущий остаток :'.$product['base']->amount,'value' => $operation['rowTransaction']->amount];
        }
        return Json::encode($answer);
    }
    //логика для ред. цены прихода товара прихода 
    private function changeStartPriceComing($data,$product,$operation)
    {
        $action = true;
        if($data['field'] === 'start_price_ua'){
            $start_price = getConvertUAHinUSD($data['value'], $operation['transaction']->course);
            $start_price_ua = $data['value'];
        }else{
            $start_price = $data['value'];
            $start_price_ua = getConvertUSDinUAH($data['value'], $operation['transaction']->course);
        }

        $operation['rowTransaction']->start_price = $start_price;
        $operation['rowTransaction']->update();
        LogService::logModel($operation['rowTransaction'], 'update');

        if(empty($product['variant'])){
            ($this->recount) ? $action = $operation['transaction']->updateProductChain($product) : null;
        }else{
            ($this->recount) ? $action = $operation['transaction']->updateVproductChain($product) : null;
        }   

        $product['base']->start_price = $product['base']->getLastStartPrice();
        $product['base']->trade_price = getTradePrice($product['base']->cost_price);
        $product['base']->update();

        if($action){
            $operation['transaction']->SaveTotalValue();
            $answer = ['status' => 'ok','value' => ['ua' => formatedPriceUA($start_price_ua),'usd' => formatedPriceUSD($start_price)],'field' => 'start_price','total_price' => ['total_usd' => formatedPriceUSD($operation['transaction']->total_usd),'total_ua' => formatedPriceUSD($operation['transaction']->total_ua)]];                
        }
        return Json::encode($answer);
    }
    //логика для ред. цены1,2 товара прихода 
    private function changePriceComing($data,$product,$operation)
    {
        if($data['value'] <= 0){
            return Json::encode(['status' => 'error','text' => 'Вы ввели значения меньше или равное 0','value' => number_format(((float)$product['base']->{$data['field']}), 2, '.', '')]);
        }
        $product['base']->{$data['field']} = $data['value'];
        $product['base']->update();
        if(!empty($product['variant'])){
            $product['variant']->{$data['field']} = $data['value'];
            $product['variant']->update();
        }
        $operation['rowTransaction']->{$data['field']} = $data['value'];
        LogService::logModel($operation['rowTransaction'], 'update');
        $operation['rowTransaction']->update();
        return Json::encode(['status' => 'ok','value' => number_format($data['value'],2)]);
    }
    //логика для ред. товара расхода 
    private function ChangeProductConsumption($data)
    {
        $product = Product::findOne($data['productId']);
        $vproduct = VProduct::findOne($data['variantId']);
        $operation = Operations::findOne($data['transaction_id']);
        $operConsumption = OperConsumption::find()->where(['product_id' => $data['productId']])->Andwhere(['vproduct_id' => empty($data['variantId']) ? null : $data['variantId']])->andWhere(['transaction_id' => $data['transaction_id']])->one(); 
        $this->recount = isset($product->date_adjustment) ? ($operation->date > $product->date_adjustment) : true;
        if($data['field'] === 'amount'){
            return $this->changeAmountConsumption($data,['base' => $product,'variant' => $vproduct],['transaction' => $operation,'rowTransaction' => $operConsumption]);
        }
        if($data['field'] === 'price'){
            return $this->changePriceConsumption($data,['transaction' => $operation,'rowTransaction' => $operConsumption]);
        }
    }
    //
    private function changeAmountConsumption($data,$product,$operation)
    {
        $difference = $this->CheckDifferenceAmount($operation, $data);
        $product['base']->amount = ($difference['mark'] === 'plus') ? $product['base']->amount - $difference['value'] : $product['base']->amount + $difference['value'];
        LogService::logModel($product['base'], 'update');
        ($this->recount) ? $product['base']->update() : null;
        $operation['rowTransaction']->amount = $data['value'];
        LogService::logModel($operation['rowTransaction'], 'update');
        $operation['rowTransaction']->update();
        if(!empty($product['variant'])){
            $product['variant']->amount = ($difference['mark'] === 'plus') ? $product['variant']->amount - $difference['value'] : $product['variant']->amount + $difference['value'];
            ($this->recount) ? $product['variant']->update() : null;
        }
        $operation['transaction']->SaveTotalValue();
        return Json::encode(['status' => 'ok','value' => $data['value'],'total_price' => ['total_usd' => formatedPriceUSD($operation['transaction']->total_usd),'total_ua' => formatedPriceUSD($operation['transaction']->total_ua)]]);
    }
    //
    private function changePriceConsumption($data,$operation)
    {
        $operation['rowTransaction']->price = $data['value'];
        LogService::logModel($operation['rowTransaction'], 'update');
        $operation['rowTransaction']->update();
        $operation['transaction']->SaveTotalValue();
        return Json::encode(['status' => 'ok','value' => formatedPriceUA($data['value']),'total_price' => ['total_usd' => formatedPriceUSD($operation['transaction']->total_usd),'total_ua' => formatedPriceUSD($operation['transaction']->total_ua)]]);
    }
    //получения как изменилось количество товара 
    protected function CheckDifferenceAmount($operation,$data)
    {
        if($data['value'] > $operation['rowTransaction']->amount){
            $difference = ['value' => $data['value'] - $operation['rowTransaction']->amount,'mark' => 'plus'];
        }else{
            $difference = ['value' => $operation['rowTransaction']->amount - $data['value'],'mark' => 'minus'];
        }
        return $difference;
    }
}