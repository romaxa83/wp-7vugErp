<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\models\Product;

class TraficSearch extends Product {

    public $start_data;
    public $end_data;

    public function rules() {
        return [
            [['name', 'agent_id', 'category_id','start_data','end_data'], 'string'],
            [['name', 'agent', 'category', 'created_at', 'warning', 'start_data', 'end_data'], 'safe'],
        ];
    }

    public function search($params) 
    {
        $query = (empty($params['TraficSearch'])) ? $query = Product::find() : Product::find()->where(['status' => 1]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => FALSE,
            'pagination' => [
                'pageSize' => 50
            ]
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->FilterWhere(['like', 'product.name', $this->name])
              ->orFilterWhere(['=', 'product.vendor_code', $this->name]);
        return $dataProvider;
    }
    
    public function getDateRange()
    {
        $start = empty($this->start_data) ? '01.01.1999 00:00:00' : $this->start_data . ' 00:00:00';
        $end = empty($this->end_data) ? '31.12.2999 23:59:59' : $this->end_data . ' 23:59:59';
        $date['start'] = date('Y-m-d H:i:s', strtotime($start));
        $date['end'] = date('Y-m-d H:i:s', strtotime($end));
        return ['between','date',$date['start'],$date['end']];
    }
}