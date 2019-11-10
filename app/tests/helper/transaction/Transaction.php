<?php

namespace app\tests\helper\transaction;

use Yii;
use app\models\Operations;
use app\models\OperComing;
use app\models\OperConsumption;
use yii\helpers\ArrayHelper;

class Transaction
{
    const COMING = 'coming';
    const CONSUMPTION = 'consumption';
    const ADJUSTMENT = 'adjustment';
    /* распределения по типам транзакций */
    public function fillTransaction(int $countTransaction,int $countProduct,string $typeTransaction) 
    {
        for($indexTransaction = 0;$indexTransaction < $countTransaction;$indexTransaction++){
            switch($typeTransaction){
                case self::COMING : 
                    $resultat[] = $this->fillComing($indexTransaction,$countProduct);
                break;

                case self::CONSUMPTION : 
                    $resultat[] = $this->fillConsumption($indexTransaction,$countProduct);
                break;
                
                case self::ADJUSTMENT : 
                    $resultat[] = $this->fillAdjustment($indexTransaction,$countProduct);
                break;
            }
        }
        
        return  $resultat;
    }   
    /* наполнения прихода */
    private function fillComing($indexTransaction,$countProduct)
    {
        $transaction = Yii::$app->db
            ->createCommand("SELECT * FROM operations WHERE type = 1 LIMIT 1 OFFSET {$indexTransaction}")
            ->queryOne();

        $totalPrice['Usd'] = $totalPrice['Ua'] = $totalPrice['CostPrice'] = 0;
            
        for($indexProduct = 1;$indexProduct < ($countProduct + 1);$indexProduct++){
            $startPrice = floatVal(rand(1, 200) . '.' . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9));
            $amount = rand(20,100);

            $data = [
                'product_id' => $indexProduct,
                'amount' => $amount,
                'start_price_ua' => 0.000,
                'start_price' => $startPrice,
                'price1' => rand(10, 100),
                'price2' => rand(10, 100),
                'transaction_id' => $transaction['id']
            ];
             
            $model = new OperComing();

            $model->load(['OperComing' => $data]);
            $resultatFilling[$transaction['id']][$indexProduct] = $model->save();

            $rowUpdate = Yii::$app->db
                ->createCommand("SELECT * FROM oper_coming WHERE product_id = {$data['product_id']} AND transaction_id = {$data['transaction_id']}")
                ->queryOne();
           
            $totalPrice['Usd'] += ($rowUpdate['start_price'] * $amount);
            $totalPrice['CostPrice'] += ($rowUpdate['cost_price'] * $amount);
        }
        $totalPrice['Ua'] = ($totalPrice['Usd'] * $transaction['course']);
        $resultatFilling['totalPrice'] = $totalPrice;
        $resultatFilling['data'] = $data;

        return $resultatFilling;
    }
    /* наполнения расхода */
    private function fillConsumption($indexTransaction,$countProduct)
    {
        $transaction = Yii::$app->db
            ->createCommand("SELECT * FROM operations WHERE type = 2 LIMIT 1 OFFSET {$indexTransaction}")
            ->queryOne();

        $keyTargetProduct = implode(',',array_keys(Yii::$app->cache->get('startDataProduct')));

        $product = Yii::$app->db
            ->createCommand("SELECT * FROM product WHERE id IN ({$keyTargetProduct}) LIMIT {$countProduct}")
            ->queryAll();

        $totalPrice['Usd'] = $totalPrice['Ua'] = $totalPrice['TradePrice'] = 0;
            
        foreach($product as $oneProduct){
            $amount = rand(1,10);

            $data = [
                'product_id' => $oneProduct['id'],
                'amount' => $amount,
                'price' => floatVal(rand(1, 200) . '.' . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9)),
                'transaction_id' => $transaction['id']
            ];

            $model = new OperConsumption();

            $model->load(['OperConsumption' => $data]);
            $resultatFilling[$transaction['id']][$oneProduct['id']] = $model->save();

            $rowUpdate = Yii::$app->db
                ->createCommand("SELECT * FROM oper_consumption WHERE product_id = {$data['product_id']} AND transaction_id = {$data['transaction_id']}")
                ->queryOne();
           
            $totalPrice['Ua'] += ($rowUpdate['price'] * $amount);
            $totalPrice['TradePrice'] += ($rowUpdate['trade_price'] * $amount);
        }
        $totalPrice['Usd'] = ($totalPrice['Ua'] / $transaction['course']);
        $resultatFilling['totalPrice'] = $totalPrice;
        $resultatFilling['data'] = $data;

        return $resultatFilling;
    }
    /* */
    public function getRandomRow(string $typeTransactionProduct,int $countChange = 5,string $stringProductId = '1,2,3,4,5')
    {
        $transactionRow = Yii::$app->db
            ->createCommand("SELECT * FROM `{$typeTransactionProduct}` WHERE product_id IN ({$stringProductId}) ORDER BY rand() LIMIT {$countChange}")
            ->queryAll();

        return ['transactionRow' => $transactionRow];
    }
}