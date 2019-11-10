<?php

namespace app\modules\order\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\order\models\Order;

class OrderSearch extends Order {

    public function rules() {
        return [
            [['order', 'date', 'amount', 'status'], 'safe']
        ];
    }

    public function search($params) {
        $query = Order::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
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
//        if (isset($this->warning) && $this->warning == 1) {
//            $query->andWhere('product.min_amount > product.amount')
//                    ->orWhere('product.amount < (product.min_amount + (20 * product.min_amount)/100)');
//        }
//        $agent_id = Agent::find()->asArray()->select(['id'])->where(['firm' => $this->agent])->one();
//        // grid filtering conditions
//        $query->andFilterWhere([
//                    'id' => $this->id,
//                    'vendor_code' => $this->vendor_code,
//                    'name' => $this->name,
//                    //'category_id' => $this->category_id,
//                    // 'agent_id' => $this->agent_id,
//                    'id_char' => $this->id_char,
////            'price1' => $this->price1,
////            'price2' => $this->price2,
//                    'status' => $this->status,
////            'created_at' => $this->created_at,
//                ])->andFilterWhere(['like', 'agent.firm', $this->agent != 'Выбрать контрагента' ? $this->agent : null])
//                ->orFilterWhere(['like', 'product.agents', !empty($agent_id) ? ':"' . $agent_id['id'] . '"' : null])
//                ->andFilterWhere(['like', 'category.name', $this->category != 'Выбрать категорию' ? $this->category : null]);
//
//        $query->andFilterWhere(['>=', 'product.created_at', $this->created_at ? strtotime($this->created_at . ' 00:00:00') : null]);
//        $query->andFilterWhere(['<=', 'product.created_at', $this->created_at ? strtotime($this->created_at . ' 23:59:59') : null]);
//
//        $query->FilterWhere(['like', 'product.name', $this->name])
//                ->orFilterWhere(['like', 'vendor_code', $this->name]);

        return $dataProvider;
    }

}
