<?php

namespace app\models;

use Yii;
use app\modules\logger\service\LogService;
/**
 * This is the model class for table "oper_adjustment".
 *
 * @property int $id
 * @property int $transaction_id
 * @property int $product_id
 * @property int $amount
 * @property string $trade_price
 * @property string $start_price
 * @property string $cost_price
 *
 * @property Product $product
 * @property Operations $transaction
 */
class OperAdjustment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oper_adjustment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['transaction_id', 'product_id', 'trade_price', 'start_price', 'cost_price'], 'required'],
            [['transaction_id', 'product_id', 'amount'], 'integer'],
            [['trade_price', 'start_price', 'cost_price'], 'number'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
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
            'transaction_id' => 'Transaction ID',
            'product_id' => 'Product ID',
            'amount' => 'Amount',
            'trade_price' => 'Trade Price',
            'start_price' => 'Start Price',
            'cost_price' => 'Cost Price',
        ];
    }
    public static function saveRow($data)
    {
        $model = new OperAdjustment();
        $model->transaction_id = $data['transaction_id'];
        $model->vproduct_id = isset($data['vproduct_id']) ? $data['vproduct_id'] : NULL;
        $model->product_id = $data['product_id'];
        $model->cost_price = $data['cost_price'];
        $model->start_price = $data['start_price'];
        $model->trade_price = $data['trade_price'];
        $model->amount = $data['amount'];
        
        LogService::logModel($model, 'create');
        
        $model->save();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransaction()
    {
        return $this->hasOne(Operations::className(), ['id' => 'transaction_id']);
    }

    public function getVproduct()
    {
        return $this->hasOne(VProduct::className(), ['id' => 'vproduct_id']);
    }
}
