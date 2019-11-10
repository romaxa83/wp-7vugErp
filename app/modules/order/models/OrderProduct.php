<?php

namespace app\modules\order\models;

use Yii;
use yii\db\ActiveRecord;
use app\models\Product;
use app\models\VProduct;

class OrderProduct extends ActiveRecord {

    public static function tableName() {
        return 'order_product';
    }

    public function rules() {
        return [
            [['order_id', 'product_id', 'vproduct_id', 'amount', 'price', 'confirm'], 'required'],
        ];
    }

    public function attributeLabels() {
        return [
            'order_id' => 'ID заказа',
            'product_id' => 'ID продукта',
            'vproduct_id' => 'ID вариативного продукта',
            'amount' => 'Количество',
            'price' => 'Цена',
            'confirm' => 'Подтвердить'
        ];
    }

    public function getProduct() {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    public function getVProduct() {
        return $this->hasOne(VProduct::className(), ['id' => 'vproduct_id']);
    }

}
