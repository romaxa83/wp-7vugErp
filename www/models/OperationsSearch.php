<?php

namespace app\models;

use \Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Operations;

/**
 * OperationsSearch represents the model behind the search form about `app\models\Operations`.
 * @property mixed storeName
 */
class OperationsSearch extends Operations
{
    public $store;



    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','type','status'], 'integer'],
            [['where', 'whence','transaction'],'string'],
            [['date','store','type','transaction'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
    public function search($params)
    {
        $query = Operations::find()->asArray()->with('whereagent')->with('whenceagent')->orderBy(['id' => SORT_DESC]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
            'pagination' => [
                'pageSize' => !getSizePage('operation') == 0 ? getSizePage('operation') : 10,
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'transaction' => $this->transaction,
            'status' => $this->status,
            'type' => $this->type,
        ]);
        
        $query->FilterWhere(['like', 'transaction', $this->transaction]);
        
        $query->andFilterWhere(['>=', 'operations.date', $this->date ? Yii::$app->formatter->asDate($this->date, 'YYYY-MM-dd 00:00:00') : null]);
        $query->andFilterWhere(['<=', 'operations.date', $this->date ? Yii::$app->formatter->asDate($this->date, 'YYYY-MM-dd 23:59:59') : null]);

        return $dataProvider;
    }
}