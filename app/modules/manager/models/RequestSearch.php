<?php

namespace app\modules\manager\models;

use Yii;
use yii\data\ActiveDataProvider;

class RequestSearch extends Request {

    public function rule()
    {
        return [
            [['store_id','status'], 'integer'],
            [['store_id','status'], 'safe']
        ];
    }

    public function search($params) {
        $query = Request::find()->orderBy(['id' => SORT_ASC]);
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

        $query->andFilterWhere([
            'id' => $this->id,
            'store_id' => $this->store_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        return $dataProvider;
    }

}
