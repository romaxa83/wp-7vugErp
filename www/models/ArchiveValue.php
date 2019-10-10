<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "archive_value".
 *
 * @property int $id
 * @property int $archive_id
 * @property string $type
 * @property int $product_id
 * @property int $amount
 * @property string $price
 * @property string $price1
 * @property string $price2
 * @property string $trade_price
 * @property string $start_price
 * @property string $cost_price
 *
 * @property Archive $archive
 */
class ArchiveValue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'archive_value';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['archive_id', 'product_id'], 'required'],
            [['archive_id', 'product_id', 'amount'], 'integer'],
            [['price', 'price1', 'price2', 'trade_price', 'start_price', 'cost_price'], 'double'],
            [['archive_id'], 'exist', 'skipOnError' => true, 'targetClass' => Archive::className(), 'targetAttribute' => ['archive_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'archive_id' => 'Archive ID',
            'type' => 'Type',
            'product_id' => 'Product ID',
            'amount' => 'Amount',
            'price' => 'Price',
            'price1' => 'Price1',
            'price2' => 'Price2',
            'trade_price' => 'Trade Price',
            'start_price' => 'Start Price',
            'cost_price' => 'Cost Price',
        ];
    }
    
    public function LoadProduct($product,$id){
        $this->archive_id = $id;
        $this->vproduct_id = $product->vproduct_id;
        $this->product_id = $product->product_id;
        $this->amount = $product->amount; 
        $this->price = isset($product->price) ? $product->price : null;
        $this->price1 = isset($product->price1) ? $product->price2 : null;
        $this->price2 = isset($product->price2) ? $product->price2 : null;
        $this->trade_price = isset($product->trade_price) ? $product->trade_price : null;
        $this->cost_price = $product->cost_price;
        $this->start_price = isset($product->start_price) ? $product->start_price : null;
        $this->save();
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArchive()
    {
        return $this->hasOne(Archive::className(), ['id' => 'archive_id']);
    }

    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    public function getVproduct()
    {
        return $this->hasOne(VProduct::className(), ['id' => 'vproduct_id']);
    }
}
