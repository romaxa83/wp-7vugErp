<?php

namespace app\modules\order\models;

use Yii;
use app\modules\order\models\OrderProduct;

class Order extends \yii\db\ActiveRecord {

    public static function tableName() {
        return 'order';
    }

    public function rules() {
        return [
            [['order', 'date', 'amount', 'status'], 'required'],
        ];
    }

    public function attributeLabels() {
        return [
            'order' => 'ID Заказа',
            'date' => 'Дата',
            'amount' => 'Сумма',
            'status' => 'Статус'
        ];
    }

    public function getOrderProduct() {
        return $this->hasMany(OrderProduct::className(), ['id' => 'order_id']);
    }

}
