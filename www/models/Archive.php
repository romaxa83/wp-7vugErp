<?php

namespace app\models;

use Yii;
use app\models\ArchiveValue;
/**
 * This is the model class for table "archive".
 *
 * @property int $id
 * @property int $type
 * @property int $transaction_id
 * @property string $transaction
 * @property int $whence
 * @property int $where
 * @property string $total_usd
 * @property string $total_ua
 * @property string $date
 * @property string $date_archive
 *
 * @property Agent $whence0
 * @property Agent $where0
 */
class Archive extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'archive';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'transaction_id', 'whence', 'where'], 'integer'],
            ['transaction_id' , 'unique', 'message' => 'Запись уже находиться в ахриве'],
            [['total_usd', 'total_ua'], 'number'],
            [['date', 'date_archive'], 'safe'],
            [['transaction'], 'string', 'max' => 50],
            [['whence'], 'exist', 'skipOnError' => true, 'targetClass' => Agent::className(), 'targetAttribute' => ['whence' => 'id']],
            [['where'], 'exist', 'skipOnError' => true, 'targetClass' => Agent::className(), 'targetAttribute' => ['where' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'transaction_id' => 'Transaction ID',
            'transaction' => 'Transaction',
            'whence' => 'Whence',
            'where' => 'Where',
            'total_usd' => 'Total Usd',
            'total_ua' => 'Total Ua',
            'date' => 'Date',
            'date_archive' => 'Date Archive',
        ];
    }
    
    public function LoadModel($model){
        $this->type = $model->type;
        $this->transaction_id = $model->id;
        $this->transaction = $model->transaction;
        $this->whence = $model->whence;
        $this->where = $model->where;
        $this->total_ua = $model->total_ua;
        $this->total_usd = $model->total_usd;
        $this->cost_price = $model->cost_price;
        $this->trade_price = isset($model->trade_price) ? $model->trade_price : null;
        $this->start_price = isset($model->start_price) ? $model->start_price : null;
        $this->date = $model->date;
        $this->date_archive = date("Y-m-d H:i:s");
        if($this->save()){
            return ['transaction_id' => $this->transaction_id,'archive_id' => $this->id,'status' => 'ok','transaction' =>$model->transaction];
        }else{
            ShowMessenge($this->getErrorSummary(true));
            return ['status' => 'error'];
        }
    }

    public function getTypeName()
    {
        $type = 'не определена';
        if($this->type == 1){
            $type = 'приход';
        } elseif($this->type == 2){
            $type = 'расход';
        }  elseif($this->type == 3){
            $type = 'коректировка';
        }
        return $type;
    }

    public function getProducts(){
        return $this->hasMany(ArchiveValue::className(),['archive_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWhence()
    {
        return $this->hasOne(Agent::className(), ['id' => 'whence']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWhere()
    {
        return $this->hasOne(Agent::className(), ['id' => 'where']);
    }
}
