<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\models\Product;

class TraficSearch extends Product {

    public $date0;
    public $date1;

    public function rules() {
        return [
            [['id', 'vendor_code', 'char_id'], 'integer'],
            [['name', 'agent_id', 'category_id'], 'string'],
            [['name', 'agent', 'category', 'created_at', 'warning', 'date0', 'date1'], 'safe'],
        ];
    }

    public function search($params) {
        $query = (isset($params['TraficSearch'])) ? $query = Product::find() : Product::find()->where(['status' => 1]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => FALSE,
            'pagination' => FALSE
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->FilterWhere(['like', 'product.name', $this->name])
              ->orFilterWhere(['=', 'product.vendor_code', $this->name]);
        return $dataProvider;
    }

}
