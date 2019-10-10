<?php

namespace app\models;

use Yii;
use app\modules\logger\service\LogService;
/**
 * This is the model class for table "oper_consumption".
 *
 * @property int $id
 * @property int $transaction_id
 * @property string $type
 * @property int $product_id
 * @property int $vproduct_id
 * @property int $amount
 * @property string $price
 * @property string $trade_price
 * @property string $cost_price
 *
 * @property Operations $transaction
 */
class OperConsumption extends \yii\db\ActiveRecord
{
    public $transfer = false;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oper_consumption';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'price', 'amount'], 'required'],
            ['transaction_id' , 'required' , 'message' => ''],
            [['transaction_id', 'product_id', 'amount'], 'integer'],
            [['price', 'trade_price', 'cost_price'], 'double'],
            ['amount','quantityAvailability'],
            [['vproduct_id'],'default', 'value'=> null],
            [['transaction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Operations::className(), 'targetAttribute' => ['transaction_id' => 'id']],
            ['vproduct_id', 'exist', 'targetClass' => VProduct::className(), 'targetAttribute' => ['vproduct_id' => 'id'],'message' => 'передан не существующий vproduct_id']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'transaction_id' => 'Transaction ID',
            'product_id' => 'Продукт',
            'amount' => 'Кол-во',
            'price' => 'Цена продажи',
            'trade_price' => 'Trade Price',
            'cost_price' => 'Cost Price',
        ];
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert) && Operations::checkDuplicates($this,$insert)){
            if ($this->isNewRecord && $this->transfer == false) {
                $product = $this->product;
                $product->amount -= $this->amount;
                
                LogService::logModel($product, 'update');
                
                $product->update();
                                
                $this->cost_price = $product->cost_price;
                $this->trade_price = $product->trade_price;
                
                if($this->vproduct_id !== NULL){
                    $v_product = $this->vproduct;
                    $v_product->amount -= $this->amount;
                    $v_product->update();
                }
                
                LogService::logModel($this, 'create');
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
            $transaction = Operations::findOne($this->transaction_id);
            $transaction->SaveTotalValue();
        }
    }
    
    public function getChangePrice()
    {
        $change = true;
        if($this->vproduct_id === null){
            --$this->product->change_price >= 0 ? $this->product->update() : $change = false;
        }else{
            --$this->vproduct->change_price >= 0 ? $this->vproduct->update() : $change = false;
        }
        return $change;
    }
    
    public function SaveVariant($data,$productTransactionId)
    {
        $data['OperConsumption']['transaction_id'] = (int)$productTransactionId['transaction_id'];
        $data['OperConsumption']['product_id'] = (int)$productTransactionId['product_id'];
        $data['OperConsumption']['vproduct_id'] = $data['product_id'];
        $data['OperConsumption']['amount'] = (int)$data['amount'];
        $data['OperConsumption']['price'] = isset($data['price1']) ? (float)$data['price1'] : (float)$data['price2'];
        if($this->load($data) || !$this->validate()){
            $this->save();
            if($this->hasErrors()){
                return $this->getErrorSummary(true);
            }
        }
    }
    public function QuantityAvailability($attribute)
    {
        if(!$this->transfer){
            $answer = true;
            $product = $this->product;
            if($this->amount > $product->amount && $this->isNewRecord){
                $this->addError($attribute,'Вы пытаеться добавить в расход количество больше чем на складе');
                $answer = false;
            }
            if(substr($this->transaction->transaction,-1) == 'A' && $this->amount < 0 && $this->isNewRecord){
                $this->addError($attribute,'Вы пытаеться добавить в расход количество больше чем на складе');
                $answer = false;
            }
            return $answer;
        }
    }
    /**
     * Метод для распределени товара при массовых транзакциях,
     * принимает 3 параметра:общее кол-во товара,кол-во товара для одной транзакции,кол-во транзакции.
     * Если для транзакции не хватит товара,им запишеться нули
     */
    public static function distributionAmount($all_amount, $amount, $count)
    {
        $arr = [];
        $midl = $all_amount;
        for ($i = 0; $i < (int)$count; ++$i) {
            if ((int)$midl > (int)$amount) {
                $arr[$i] = $amount;
                $midl -= $amount;
            } elseif ((int)$midl <= (int)$amount) {
                $arr[$i] = $midl;
                $midl = 0;
            } else {
                $arr[$i] = 0;
            }
        }
        return $arr;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransaction()
    {
        return Operations::findOne($this->transaction_id);
    }

    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }
    
    public function getVproduct()
    {
        return $this->hasOne(VProduct::className(), ['id' => 'vproduct_id']);
    }
    
    public function getCategory()
    {
        return $this->hasMany(Category::className(),['id' => 'category_id'])
            ->viaTable('product',['id' => 'product_id']);
    }
}
