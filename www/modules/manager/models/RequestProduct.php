<?php

namespace app\modules\manager\models;

use app\models\Agent;
use app\models\Product;
use app\models\VProduct;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * @property integer $id
 * @property int request_id
 * @property int $product_id
 * @property int $vproduct_id
 * @property int $amount
 * @property int $price
 * @property int $cost_price
 * @property int $trade_price
 *
 */

class RequestProduct extends ActiveRecord
{
    public $name;
    const NEW_ROW = 'scenario for new row prodcut';
    const EDIT_ROW = 'scenario for edit row product';

    const SUCCESS_ADD = 1;
    const ERROR_ADD = 2;
    const DUPLICATE_ADD = 3;

    public static function tableName()
    {
        return '{{%request_product}}';
    }


    public function rules()
    {
        return [
            ['product_id', 'unique', 'when' => function(){
                return RequestProduct::find()->where(['product_id' => $this->product_id])->andWhere(['request_id' => $this->request_id])->exists();
            }, 'on' => self::NEW_ROW],
            [['request_id','price','cost_price','trade_price'], 'required' , 'on' => self::NEW_ROW],
            ['product_id', 'required', 'message' => 'Продукт не выбран', 'on' => self::NEW_ROW],
            ['amount',  'default', 'value' => 0],
            ['amount', 'compare', 'compareValue' => 0, 'operator' => '>=', 'type' => 'number'],
            [['request_id','vproduct_id','product_id','amount'], 'integer'],
            ['vproduct_id',  'default', 'value' => null],
            ['vproduct_id', 'exist', 'targetClass' => VProduct::className(), 'targetAttribute' => ['vproduct_id' => 'id'],'message' => 'передан не существующий vproduct_id'],
            [['price','cost_price','trade_price'], 'double'],
        ];
    }
    //после добавления товара помечаем если заявка была пуста что не пустая но не сформирована
    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        Request::updateAll(['status' => Request::REQUEST_NOT_EMPTY],['and',['id' => $this->request_id],['status' => Request::REQUEST_INACTIVE]]);
    }
    //сохранения позиций заявки 
    public function saveProduct($typePrice)
    {
        $duplicate = self::find()->where(['request_id' => $this->request_id])->andWhere(['product_id' => $this->product_id])->exists();
        if($duplicate){
            return ['status' => self::DUPLICATE_ADD,'model' => self::find()->where(['product_id' => $this->product_id])->andWhere(['request_id' => $this->request_id])->one()];
        }else{
            $product = Product::find()
                ->select(["price{$typePrice} as price",'cost_price','trade_price'])
                ->asArray()
                ->where(['id' => $this->product_id])
                ->one();
            $this->price = (double)$product['price'];
            $this->cost_price = (double)$product['cost_price'];
            $this->trade_price = (double)$product['trade_price'];
            return ['status' => $this->save() ? self::SUCCESS_ADD : self::ERROR_ADD,'model' => $this];
        }
    }
    //изменяем количество у позиций заявки 
    public static function ChangeAmount($product_id,$request_id,$amount)
    {
        $model = self::find()->where(['product_id' => $product_id])->andWhere(['request_id' => $request_id])->one();
        $model->amount = $amount;
        if($model->save()){
            return ['status' => true, 'model' => $model];
        }else{
            return ['status' => false, 'model' => $model];
        }
    }
    //удаления продукта из заявки
    public static function deleteProduct($product_id,$request_id)
    {
        $model = self::find()->where(['product_id' => $product_id])->andWhere(['request_id' => $request_id])->one();
        if($model->delete()){
            return ['status' => true];
        }else{
            return ['status' => false];
        }
    }
    //Relation
    public function getStoreName()
    {
         return $this->hasOne(Agent::className(),['id' => 'store_id'])
            ->viaTable('request',['id' => 'request_id']);
    }

    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    public function getVproduct()
    {
        return $this->hasOne(VProduct::class, ['id' => 'vproduct_id']);
    }
}