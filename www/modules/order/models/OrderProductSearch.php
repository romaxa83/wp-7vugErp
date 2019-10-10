<?php

namespace app\modules\order\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\order\models\OrderProduct;

class OrderProductSearch extends OrderProduct {

    public function rules() {
        return [
            [['order_id', 'product_id', 'amount', 'price', 'confirm'], 'safe']
        ];
    }

    public function search($params, $id) {
        $query = OrderProduct::find()->asArray()->where(['order_id' => $id])->with(['vProduct', 'product.category']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ]
            ],
            'pagination' => [
                'pageSize' => !getSizePage('prod') == 0 ? getSizePage('prod') : 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }

}
