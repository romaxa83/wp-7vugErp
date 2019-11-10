<?php

namespace app\modules\manager\models;

use app\models\Agent;
use yii\db\ActiveQuery;
use app\models\Product;
use app\models\Category;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
/**
 * @property integer $id
 * @property string $comment
 * @property int $store_id
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 */

class Request extends ActiveRecord
{
    const REQUEST_NOT_EMPTY = 3;
    const REQUEST_ACTIVE = 1;
    const REQUEST_INACTIVE = 0;

    public static function tableName() : string
    {
        return '{{%request}}';
    }


    public function rules() : array
    {
        return [
            [['store_id'], 'required'],
            [['comment'], 'string'],
        ];
    }

    public function attributeLabels() : array
    {
        return [
            'comment' => 'Коментарий',
            'store_id' => 'Магазин',
            'status' => 'Статус',
        ];
    }
//relation
    public function getStore(): ActiveQuery
    {
        return $this->hasOne(Agent::class, ['id' => 'store_id']);
    }

    public function getProducts(): ActiveQuery
    {
        return $this->hasMany(RequestProduct::class, ['request_id' => 'id']);
    }

    //возвращает обще кол-во товара по всем заявка
    public function countProductRequests($product_id,$vproduct_id=null)
    {
        return array_sum(ArrayHelper::getColumn($this->getRequestProduct($product_id,$vproduct_id),'amount'));
    }

    public function anotherStore($product_id,$vproduct_id=null)
    {
        $product = $this->getRequestProduct($product_id,$vproduct_id);
        $stores = [];
        foreach ($product as $key => $one){
            if($one->request_id != $this->id){
                $stores[$key]['firm'] = $one->getStoreName()->select('firm')->one()['firm'];
                $stores[$key]['amount'] = $one->amount;
            }
        }
        return $stores;
    }

    private function getRequestProduct($product_id,$vproduct_id=null)
    {
        return RequestProduct::find()->where(['product_id' => $product_id])->andWhere(['vproduct_id' => $vproduct_id])->all();
    }

    public function getRowTable()
    {
        $row = [];
        $typePrice = $this->getStore()->select('price_type')->asArray()->one();
        $product = ArrayHelper::index($this->getProducts()->all(),'product_id');
        $keys = array_keys($product);
        $productDb = ArrayHelper::index(Product::find()->asArray()->select(['category_id','id','cost_price','trade_price','price'.$typePrice['price_type'],'amount','name'])->where(['in','id',$keys])->all(),'id');
        $category = ArrayHelper::index(category::find()->select(['name','id'])->asArray()->all(),'id');
        foreach($product as $key => $one){
            $one->cost_price = $productDb[$key]['cost_price'];
            $one->price = $productDb[$key]['price'.$typePrice['price_type']];
            $one->trade_price = $productDb[$key]['trade_price'];
            $product[$key]->update();
            $row[$key]['request_id'] = $this->id;
            $row[$key]['product_id'] = $one->product_id; 
            $row[$key]['vproduct_id'] = $one->vproduct_id; 
            $row[$key]['productName'] = $productDb[$key]['name'];
            $row[$key]['categoryName'] = $category[$productDb[$key]['category_id']]['name'];
            $row[$key]['amountOtherStore'] = $this->anotherStore($one->product_id,$one->vproduct_id);
            $row[$key]['amountStock'] = $productDb[$key]['amount'];
            $row[$key]['amount'] = $one->amount;
            $row[$key]['price'] = $one->price;
        }
        ArrayHelper::multisort($row, ['categoryName'], SORT_ASC);
        return $row;
    }
    //формирования заявки 
    public function confirmRequest($comment)
    {
        $this->comment = $comment;
        $this->status = Request::REQUEST_ACTIVE;
        $this->updated_at = time();
        return $this->update();
    }
    //очистка заявки 
    public function clearRequest()
    {
        RequestProduct::deleteAll(['request_id' => $this->id]);
        Request::updateAll(['status' => Request::REQUEST_INACTIVE],['id' => $this->id]);
    }
}
