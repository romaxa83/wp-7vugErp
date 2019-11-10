<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Access;

/**
 * OperationsSearch represents the model behind the search form about `app\models\Operations`.
 * @property mixed storeName
 */
class AccessSearch extends Access {

    public $store;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
//            [['id', 'transaction', 'type', 'operation_id', 'status'], 'integer'],
//            [['where', 'whence', 'prod_value', 'old_value'], 'string'],
//            [['date', 'store', 'type'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = Access::find();

//        $query->joinWith(['store']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
//            'sort' => [
//                'defaultOrder' => [
//                    'id' => SORT_DESC,
//                ]
//            ],
            'pagination' => [
                'pageSize' => !getSizePage('operation') == 0 ? getSizePage('operation') : 10,
            ]
        ]);

//        $dataProvider->sort->attributes['store'] = [
//            'asc' => ['agent.firm' => SORT_ASC],
//            'desc' => ['agent.firm' => SORT_DESC],
//        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
//        $query->andFilterWhere([
//            'id' => $this->id,
//            'transaction' => $this->transaction,
////            'whence' => $this->whence,
////            'where' => $this->where,
//            'status' => $this->status,
//            'type' => $this->type,
//            'date' => $this->date,
//        ]);
//            ->andFilterWhere(['like', 'agent.firm', $this->store]);
//            ->andFilterWhere(['like','transaction',$this->transaction != 'Номер транзакции' ? $this->transaction : null]);

        return $dataProvider;
    }

}
