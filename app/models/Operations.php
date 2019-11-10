<?php

namespace app\models;

use Exception;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\StringHelper;
use app\modules\logger\ActiveRecordBehavior;
use kartik\mpdf\Pdf;
use app\models\OperComing;
use app\models\OperConsumption;
use app\modules\logger\service\LogService;
/**
 * This is the model class for table "operations".
 *
 * @property integer $id
 * @property integer $transaction
 * @property integer $operation_id
 * @property integer $prod_id
 * @property integer $whence
 * @property integer $where
 * @property integer $amount
 * @property string $price
 * @property integer $type
 * @property string $date
 * @property string $date_update
 * @property integer $total_usd
 * @property integer $total_ua
 * @property integer $start_price
 * @property integer $cost_price
 * @property integer $trade_price
 * @property integer $course
 */
class Operations extends ActiveRecord
{

    const OPERATION_EMPTY = 0;
    const OPERATION_FULL = 1;

    public function transactions()
    {
        return [
            ActiveRecord::SCENARIO_DEFAULT => ActiveRecord::OP_ALL,
        ];
    }

//    public function behaviors()
//    {
//        return [
//            [
//                'class' => ActiveRecordBehavior::class,
//                // Список полей за изменением которых будет производиться слежение
//                // можно использовать свои методы и связи с другой моделью
//                // @see https://github.com/lav45/yii2-activity-logger
//                'attributes' => [
//                    'id',
//                    'transaction',
//                    'operation_id',
//                    'old_value',
//                    'whence',
//                    'where',
//                    'prod_value',
//                    'status',
//                    'type',
//                    'date',
//                    'total_usd',
//                    'total_ua',
//                    'cource',
//                    'trade_price',
//                    'start_price',
//                    'cost_price',
//                    'date_update',
//                    'recalculated',
//                ]
//            ]
//        ];
//    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'operations';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['whence', 'where', 'date'], 'required'],
            [['whence', 'where', 'type'], 'integer'],
            [['where', 'type', 'recalculated'], 'integer'],
            [['prod_value', 'date'], 'string'],
            [['date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'transaction' => '№ опер.',
            'operation_id' => 'Стогнирующая операция',
            'old_value' => 'Старые значения',
            'whence' => 'Откуда',
            'where' => 'Куда',
            'prod_value' => 'Кол-во',
            'status' => 'Статус',
            'type' => 'Тип',
            'total_price_usd' => 'Общая цена(usd)',
            'total_price_ua' => 'Общая цена(ua)',
            'date' => 'Дата',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)){
            if ($this->isNewRecord){
                $this->date = empty($this->date) ? date("Y-m-d H:i:s") : $this->date;
                $this->course = Settings::getUsd();
                LogService::logModel($this, 'create');
            } else {
                $this->date_update = date("Y-m-d H:i:s");
                LogService::logModel($this, 'update');
            }
            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if($insert){
            $this->transaction = $this->id . '00' . rand(100,999) . $this->transaction;
            $this->update();
        }
    }
    
    public function SaveTotalValue()
    {
        if($this->type === 1){
            $this->SaveTotalComing();
        }elseif($this->type === 2){
            $this->SaveTotalConsumption();
        }elseif($this->type === 3) {
            $this->SaveTotalAdjustment();
        }
        $this->update();
    }
    
    protected function SaveTotalComing(){
        $product = OperComing::find()->asArray()->where(['transaction_id' => $this->id])->all();
        $total_start_price = 0;
        $total_cost = 0;
        foreach($product as $one){
            $total_start_price += $one['start_price'] * $one['amount'];
            $total_cost += $one['cost_price'] * $one['amount'];
        }
        $this->cost_price = $total_cost;
        $this->start_price = $total_start_price;
        $this->total_usd = $total_start_price;
        $this->total_ua = getConvertUSDinUAH($total_start_price, $this->course);
    }
    
    protected function SaveTotalConsumption(){
        $product = OperConsumption::find()->asArray()->where(['transaction_id' => $this->id])->all();
        $total_price = 0;
        $total_trade = 0;
        $total_cost = 0;
        foreach($product as $one){
            $total_price += $one['price'] * $one['amount'];
            $total_cost += $one['cost_price'] * $one['amount'];
            $total_trade += $one['trade_price'] * $one['amount'];
        }
        $this->total_ua = $total_price;
        $this->total_usd = str_replace(',','.',getConvertUAHinUSD($total_price, $this->course));
        $this->cost_price = $total_cost;
        $this->trade_price = $total_trade;
    }
    
    protected function SaveTotalAdjustment(){
        $product = $this->products;
        $total_start = 0;
        $total_cost = 0;
        $total_trade = 0;
        foreach($product as $one){
            $total_start += $one['start_price'] * $one['amount'];
            $total_cost += $one['cost_price'] * $one['amount'];
            $total_trade += $one['trade_price'] * $one['amount'];
        }
        $this->start_price = $total_start;
        $this->cost_price = $total_cost;
        $this->trade_price = $total_trade;
    }

    public function isProductExist($id)
    {
        return in_array($id, array_column($this->products, 'product_id'));
    }

    public function getNotExistVProducts($id)
    {
        $product = Product::findOne($id);
        $v_products = $product->getVproducts()->asArray()->all();
        $exist_v_products = $this->getProducts()->where(['product_id' => $product->id])->asArray()->all();
        $not_exist_array = array_diff(array_column($v_products,'id'), array_column($exist_v_products,'vproduct_id'));
        return $product->getVproducts()->where(['in', 'id', $not_exist_array])->all();
    }

    public function getWhenceagent()
    {
        return $this->hasOne(Agent::className(), ['id' => 'whence']);
    }

    public function getWhereagent()
    {
        return $this->hasOne(Agent::className(), ['id' => 'where']);
    }
    
    public function getProducts()
    {
        if($this->type === 1){
            return $this->hasMany(OperComing::className(),['transaction_id' => 'id']);
        }elseif($this->type === 2) {
            return $this->hasMany(OperConsumption::className(),['transaction_id' => 'id']);
        }elseif($this->type === 3) {
            return $this->hasMany(OperAdjustment::className(),['transaction_id' => 'id']);
        }
    }

    public function getTypeName()
    {
        $type = 'не определена';
        if($this->type == 1){
            $type = 'приход';
        } elseif($this->type == 2){
            $type = 'расход';
        }  elseif($this->type == 3){
            $type = 'коректировка';
        }
        return $type;
    }

    public static function getNameCharsAndValue($arr_var_prod)
    {
        if (!empty($arr_var_prod)){
            $char_name = [];
            $val_name = [];
            foreach ($arr_var_prod as $id => $one){
                $arr = unserialize($one['char_value']);
                foreach ($arr as $key => $value){
                    $char_name[] = $key;
                    $val_name[] = $value;

                }
            }
            $arr_char_name = array_unique($char_name);
            $arr_val_name = array_unique($val_name);
            $name_char = [];
            foreach($arr_char_name as $k => $v){
                $char =  Characteristic::find()->where(['id' => $v])->one();
                $name_char[$k][] = $char['name'];
                foreach ($arr_val_name as $k1 => $v1){
                    if(CharacteristicValue::find()->where(['id' => $v1,'id_char' => $v])->exists()){
                        $ch = CharacteristicValue::find()->where(['id' => $v1,'id_char' => $v])->asArray()->one();
                        $name_char[$k][$v1] = $ch['name'];
                    }
                }
            }
            return $name_char;
        } else {
            return false;
        }
    }
    
    public function getPdf($operation, $orientation = 0)
    {
        return new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => ($orientation == 0) ? Pdf::ORIENT_PORTRAIT : Pdf::ORIENT_LANDSCAPE,
            'content' => $operation,
            'options' => [
                'language' => 'ru_RU',
                'title' => getRequisites('name_firm'),
                'subject' => 'Generating PDF files via yii2-mpdf extension has never been easy'
            ],
            'methods' => [
                'SetHeader' => [getRequisites('name_firm')],
                'SetFooter' => [''],
            ]
        ]);
    }
    //метод пересчтёта цепочки транзакций для базового товара 
    public function updateProductChain($product,$difference = null)
    {
        $operations = self::find()->where(['>=','operations.id',$this->id])->leftJoin('oper_coming', 'oper_coming.transaction_id = operations.id')->andWhere(['oper_coming.product_id' => $product['base']->id])->all();

        for($i=0;$i<count($operations);$i++){   
            $current = $this->getRowProductForUpdateChain($operations,$product,$i);
            $next = $this->getRowProductForUpdateChain($operations,$product,$i + 1);

            $currentCostPrice = getNewCostPrice($current->old_cost_price, $current->old_amount, $current->start_price, $current->amount);
            $current->cost_price = $currentCostPrice;
            $current->update();
            LogService::logModel($current, 'update');

            if(!empty($next)){
                if($difference != null) {
                    $next->old_amount = ($difference['mark'] === 'plus') ? $next->old_amount + $difference['value'] : $next->old_amount - $difference['value'];                    
                }
                
                $next->old_cost_price = $currentCostPrice;
                $nextCostPrice = getNewCostPrice($next->old_cost_price, $next->old_amount, $next->start_price, $next->amount);

                $next->cost_price = $nextCostPrice;                
                $next->update();
                LogService::logModel($next, 'update');
            }
            $operations[$i]->SaveTotalValue();
        }
        $product['base']->cost_price = isset($nextCostPrice) ? $nextCostPrice : $currentCostPrice;
        $product['base']->trade_price = getTradePrice($product['base']->cost_price);
        $product['base']->update();
        LogService::logModel($product['base'], 'update');

        return true;
    }
    //метод пересчтёта цепочки транзакций для вариативного товара 
    public function updateVproductChain($product,$difference = null)
    {
        $operations = self::find()->where(['>=','operations.id',$this->id])->leftJoin('oper_coming', 'oper_coming.transaction_id = operations.id')->andWhere(['oper_coming.product_id' => $product['base']->id])->all();
        $i = 0; 
        foreach ($operations as $key => $one){
            $products = ($key === 0) ? $this::ArrayBolting($one->products,$product) : $this::ArrayBolting($one->products,$product,true);
            for($y=0;$y<count($products);$y++){
                if($key !== 0 && $y === 0){
                    $products[$y]->old_amount = ($difference['mark'] === 'plus') ? $products[$y]->old_amount + $difference['value'] : $products[$y]->old_amount - $difference['value'];
                    $products[$y]->old_cost_price = $currentCostPrice;
                }
                $currentCostPrice = getNewCostPrice($products[$y]->old_cost_price, $products[$y]->old_amount, $products[$y]->start_price, $products[$y]->amount);
                $products[$y]->cost_price = $currentCostPrice;
                $products[$y]->update();
                if(isset($products[$y + 1])){
                    if($difference != null) {
                        $products[$y + 1]->old_amount = ($difference['mark'] === 'plus') ? $products[$y + 1]->old_amount + $difference['value'] : $products[$y + 1]->old_amount - $difference['value'];
                    }
                    $products[$y + 1]->old_cost_price = $currentCostPrice;
                }                
            }
            $i++;
            $operations[$key]->SaveTotalValue();
        }
        $product['base']->cost_price = $currentCostPrice;
        $product['base']->update();
        return true;
    }
    //фильтрация от вариаций которые ниже редактированой позиций
    public static function ArrayBolting($products,$product,$flag = null)
    {
        foreach($products as $key => $one){
            if($flag == null){
                if($one->vproduct_id != $product['variant']->id){
                    unset($products[$key]);
                }else{
                    break;
                }
            }else{
                if($one->product_id != $product['base']->id){
                    unset($products[$key]);
                }else{
                    break;
                }
            }
        }
        return array_values($products);
    }
    //получения строчки базового тоавара
    private function getRowProductForUpdateChain($operations,$product,$i)
    {
        if(!isset($operations[$i])){
            return ;
        }
        foreach ($operations[$i]->products as $oneProduct){
            if($oneProduct->product_id !== $product['base']->id){
                continue;
            }
            return $oneProduct;
        }
    }
    
    public function AmountChangeToZeroRecount($product){
        if(!empty($product['variant'])){return $this->AmountVproductChangeToZeroRecount($product);}
        $rowProduct = OperComing::find()->where(['>=','transaction_id',$this->id])->andWhere(['product_id' => $product['base']->id])->limit(2)->all();
        $operation = self::find()->where(['>','operations.id',$this->id])->leftJoin('oper_coming', 'oper_coming.transaction_id = operations.id')->andWhere(['oper_coming.product_id' => $product['base']->id])->one();
        
        if(isset($rowProduct[1])){
            $rowProduct[1]->old_amount = $rowProduct[0]->old_amount;
            $rowProduct[1]->old_cost_price = $rowProduct[0]->old_cost_price;
            $rowProduct[1]->update(); 

            $product['base']->price1 = $rowProduct[1]->price1;
            $product['base']->price2 = $rowProduct[1]->price2;
        }

        $lastCostPrice = $rowProduct[0]->old_cost_price;
        $amount = $rowProduct[0]->amount;

        $rowProduct[0]->amount = 0;
        $rowProduct[0]->update(); 

        $product['base']->amount -= $amount;
        if(!empty($operation)){
            $operation->updateProductChain($product,['mark' => 'minus','value' => $amount]);
        }else{
            $product['base']->cost_price = $lastCostPrice;
            $product['base']->trade_price = getTradePrice($lastCostPrice);
        }
        $product['base']->start_price = $product['base']->getLastStartPrice();

        return $product['base']->update();
    }
    
    private function AmountVproductChangeToZeroRecount($product){
        $rowProduct[0] = OperComing::find()->where(['transaction_id' => $this->id])->andWhere(['product_id' => $product['base']->id])->andWhere(['vproduct_id' => $product['variant']->id])->one();
        $rowProduct[1] = OperComing::find()->where(['>=','transaction_id',$this->id])->andWhere(['>','id',$rowProduct[0]->id])->andWhere(['product_id' => $product['base']->id])->one();
        $amount = $rowProduct[0]->amount;
        $rowProduct[0]->amount = 0;
        $rowProduct[0]->update();
        if(!empty($rowProduct[1])){
            $rowProduct[1]->old_amount = $rowProduct[0]->old_amount;
            $rowProduct[1]->old_cost_price = $rowProduct[0]->old_cost_price;
            $rowProduct[1]->update(); 
            $product['variant'] = VProduct::findOne($rowProduct[1]->vproduct_id);
            if($rowProduct[0]->transaction_id == $rowProduct[1]->transaction_id){
                $this->updateVproductChain($product, ['mark' => 'minus','value' => $amount]);
            }else{
                $rowProduct[1]->transaction->updateVproductChain($product, ['mark' => 'minus','value' => $amount]);
            }
        }else{
            $product['base']->cost_price = $rowProduct[0]->old_cost_price;
        }
        $product['base']->amount -= $amount;
        $product['variant']->amount -= $amount;
        $product['variant']->update();
        return $product['base']->update(); 
    }

    public static function checkDuplicates($row,$insert){
        $class = StringHelper::basename(get_class($row));
        $vproduct_id = empty($row->vproduct_id) ? null : $row->vproduct_id;
        if($class == 'OperComing'){
            $rowFromDb = OperComing::find()->where(['product_id' => $row->product_id])->andWhere(['vproduct_id' => $vproduct_id])->andWhere(['transaction_id' => $row->transaction_id])->one();
        }
        if($class == 'OperConsumption'){
            $rowFromDb = OperConsumption::find()->where(['product_id' => $row->product_id])->andWhere(['vproduct_id' => $vproduct_id])->andWhere(['transaction_id' => $row->transaction_id])->one();
        }
        if(!$insert){
            return true;
        }else{
            if(empty($rowFromDb)){
                return true;
            }else{
                $row->addError('Duplicates','Товар повторяеться');
                return false;
            }
        }
    }
}
