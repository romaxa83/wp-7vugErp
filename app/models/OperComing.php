<?php

namespace app\models;

use app\models\Operations;
use app\modules\logger\service\LogService;
/**
 * This is the model class for table "oper_coming".
 *
 * @property int $id
 * @property int $transaction_id
 * @property string $type
 * @property int $product_id
 * @property int $vproduct_id
 * @property int $amount
 * @property string $price1
 * @property string $price2
 * @property string $start_price
 * @property string $cost_price
 * @property int $old_amount
 * @property string $old_cost_price
 *
 * @property Operations $transaction
 */
class OperComing extends \yii\db\ActiveRecord
{
    public $transfer = false;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oper_coming';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['transaction_id', 'product_id', 'price1', 'price2', 'amount'], 'required'],
            [['transaction_id', 'product_id', 'amount', 'old_amount'], 'integer'],
            [['amount'], 'integer', 'min' => 1, 'when' => function(){
                return $this->isNewRecord && $this->transfer == FALSE;
            }],
//            [['price1','price2'], 'number', 'min' => 0.0001, 'when' => function(){
//                return !$this->transfer;
//            }],
            [['start_price'], 'compare', 'operator'=>'>', 'compareValue'=>0],
            [['amount', 'start_price'], 'required'],
            [['price1', 'price2', 'start_price', 'cost_price', 'old_cost_price'], 'number'],
            [['transaction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Operations::className(), 'targetAttribute' => ['transaction_id' => 'id']],
            [['vproduct_id'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'transaction_id' => 'Транзакция',
            'product_id' => 'Продукт',
            'amount' => 'Количество',
            'price1' => 'Цена 1',
            'price2' => 'Цена 2',
            'start_price' => 'Цена прихода',
            'cost_price' => 'Себестоимость',
            'old_amount' => 'Old Amount',
            'old_cost_price' => 'Old Cost Price',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert) && Operations::checkDuplicates($this,$insert)){
            if($this->isNewRecord && $this->transfer == false) {
                $product = $this->product;
                $this->old_amount = $product->amount;
                $this->old_cost_price = $product->cost_price;

                $product->setNewAgents($this->transaction->whence);
                $product->cost_price = getNewCostPrice($product->cost_price,$product->amount,$this->start_price,$this->amount);
                $product->amount += $this->amount;
                if($this->vproduct_id === NULL){
                    ($this->price1 != $product->price1) ? $product->price1 = $this->price1 : '';
                    ($this->price2 != $product->price2) ? $product->price2 = $this->price2 : '';
                }
                $product->start_price = $this->start_price;
                $product->trade_price = getTradePrice($product->cost_price);
                
                LogService::logModel($product, 'update');
                
                $product->update();
                
                $this->cost_price = $product->cost_price;
                
                if($this->vproduct_id !== NULL){
                    $v_product = $this->vproduct;
                    $v_product->price1 = $this->price1;
                    $v_product->price2 = $this->price2;
                    $v_product->amount += $this->amount;
                    $v_product->update();
                }
                
                LogService::logModel($this, 'create');
            }
            return true;
        }else {
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
    
    public function SaveVariant($data,$OperComing)
    {
        $data['OperComing']['transaction_id'] = $OperComing['transaction_id'];
        $data['OperComing']['product_id'] = $OperComing['product_id'];
        $data['OperComing']['vproduct_id'] = $data['product_id'];
        $data['OperComing']['amount'] = $data['amount'];
        $data['OperComing']['price1'] = $data['price1'];
        $data['OperComing']['price2'] = $data['price2'];
        $data['OperComing']['start_price'] = $OperComing['start_price'];
        if($this->load($data)){
            $this->save();
            return $this->getErrorSummary(true);
        }
    }

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
