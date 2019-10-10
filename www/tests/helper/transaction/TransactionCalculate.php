<?php

namespace app\tests\helper\transaction;

use Yii;
use yii\db\Expression;

class TransactionCalculate
{
    const COMING = 'coming';
    const CONSUMPTION = 'consumption';

    const DELETE_COMING = 'delete-product-coming';
    const DELETE_CONSUMPTION = 'delete-product-consumption';
    /* */
    public function calculateData(string $typeCalculate,int $productId)
    {
        switch($typeCalculate){
            case self::COMING : 
                return $this->comingChain($productId);
            break;

            case self::CONSUMPTION : 
                return $this->consumptionChain($productId);
            break;

            case self::DELETE_COMING : 
                return $this->comingChainWithDelete($productId);
            break;

            case self::DELETE_CONSUMPTION : 
                return $this->consumptionChainWithDelete($productId);
            break;

            default : break;
        }
    }
    /* пресчет цепочки прихода */
    private function comingChain($id)
    {
        $rowTransaction = Yii::$app->db
            ->createCommand("SELECT * FROM `oper_coming` WHERE product_id = {$id} ORDER BY id ASC")
            ->queryAll();
        $startDataProduct = Yii::$app->cache->get('startDataProduct');
        $currentProduct = $startDataProduct[$id];

        for($indexRow = 0;$indexRow < count($rowTransaction);$indexRow++){
            $oneRow = $rowTransaction[$indexRow];
            $currentProduct['old_cost_price'] = $currentProduct['cost_price'];
            $currentProduct['old_amount'] = $currentProduct['amount'];

            if($oneRow['amount'] > 0){
                $currentProduct['start_price'] = $oneRow['start_price'];
                $currentProduct['cost_price'] = (($currentProduct['cost_price'] * $currentProduct['amount']) + ($currentProduct['start_price'] * $oneRow['amount']))/($oneRow['amount'] + $currentProduct['amount']); 
                $currentProduct['trade_price'] = getTradePrice($currentProduct['cost_price']);
                $currentProduct['amount'] += $oneRow['amount'];
                $currentProduct['price1'] = $oneRow['price1'];
                $currentProduct['price2'] = $oneRow['price2'];
            }else{
                if(count($rowTransaction) == ($indexRow + 1)){
                    $currentProduct['trade_price'] = getTradePrice($currentProduct['old_cost_price']);
                }else{
                    $currentProduct['trade_price'] = getTradePrice($currentProduct['cost_price']);
                }
            }

            $resultat[$oneRow['transaction_id']] = $currentProduct; 
        }
        return $resultat;
    }
    /* пресчет цепочки прихода при удалений */
    private function comingChainWithDelete($id)
    {
        $rowTransaction = Yii::$app->db
            ->createCommand("SELECT * FROM `oper_coming` WHERE product_id = {$id}")
            ->queryAll();
        $startDataProduct = Yii::$app->cache->get('startDataProduct');
        $currentProduct = $startDataProduct[$id];

        foreach($rowTransaction as $oneRow){
            $currentProduct['old_cost_price'] = $currentProduct['cost_price'];
            $currentProduct['old_amount'] = $currentProduct['amount'];

            $currentProduct['start_price'] = $oneRow['start_price'];
            $currentProduct['cost_price'] = (($currentProduct['cost_price'] * $currentProduct['amount']) + ($currentProduct['start_price'] * $oneRow['amount']))/($oneRow['amount'] + $currentProduct['amount']); 
            $currentProduct['trade_price'] = getTradePrice($currentProduct['cost_price']);
            $currentProduct['amount'] += $oneRow['amount'];
            $currentProduct['price1'] = $oneRow['price1'];
            $currentProduct['price2'] = $oneRow['price2'];

            $resultat[$oneRow['transaction_id']] = $currentProduct; 
        }    

        return $resultat ?? $currentProduct;
    }
    /* пресчет цепочки расхода */
    private function consumptionChain($id)
    {
        $rowTransaction = Yii::$app->db
            ->createCommand("SELECT * FROM `oper_consumption` WHERE product_id = {$id} ORDER BY id ASC")
            ->queryAll();
        $startDataProduct = Yii::$app->cache->get('startDataProduct');
        $currentProduct = $startDataProduct[$id];

        foreach($rowTransaction as $oneRow){
            $currentProduct['amount'] -= $oneRow['amount'];

            $resultat[$oneRow['transaction_id']] = $currentProduct; 
        }
        
        return $resultat;
    }
    /* пресчет цепочки расхода при удалений */
    private function consumptionChainWithDelete($id)
    {
        $rowTransaction = Yii::$app->db
            ->createCommand("SELECT * FROM `oper_consumption` WHERE product_id = {$id} ORDER BY id ASC")
            ->queryAll();
        $startDataProduct = Yii::$app->cache->get('startDataProduct');
        $currentProduct = $startDataProduct[$id];

        foreach($rowTransaction as $oneRow){
            $currentProduct['old_amount'] = $currentProduct['amount'];
            $currentProduct['amount'] -= $oneRow['amount'];

            $resultat[$oneRow['transaction_id']] = $currentProduct; 
        }    

        return $resultat ?? $currentProduct;
    }
}