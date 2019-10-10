<?php

namespace module\manager\model;

use app\models\Product;
use app\modules\manager\models\Request;
use app\modules\manager\models\RequestProduct;
use app\modules\manager\services\TransactionService;
use app\tests\fixtures\AgentFixture;
use app\tests\fixtures\ManyProductFixture;
use app\tests\fixtures\CategoryFixture;
use app\tests\fixtures\StoreFixture;
use app\tests\fixtures\RequestFixture;
use app\tests\fixtures\RequestProductFixture;
use app\models\Agent;
use app\models\Operations;

class RequestTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    private $transactionService;
    
    protected function _before()
    {
        $this->transactionService = new TransactionService();

        $this->tester->haveFixtures([
            'product' => [
                'class' => ManyProductFixture::className(),
            ],
            'agent' => [
                'class' => AgentFixture::className(),
            ],
            'category' => [
                'class' => CategoryFixture::className(),
            ],
            'store' => [
                'class' => StoreFixture::className()
            ],
            'request' => [
                'class' => RequestFixture::className()
            ],
            'request-product' => [
                'class' => RequestProductFixture::className()
            ]
        ]);
    }

    protected function _after()
    {
        RequestProduct::deleteAll();
        Request::deleteAll();
        \app\models\Product::deleteAll();
        \app\models\Category::deleteAll();
        \app\models\Agent::deleteAll();
    }
    // tests
    public function testDeleteProduct()
    {
        $product = \Yii::$app->db
            ->createCommand("SELECT * FROM request_product ORDER BY rand()")
            ->queryOne();
        $resultat = RequestProduct::deleteProduct($product['product_id'],$product['request_id']);

        $this->assertTrue($resultat['status']);
    }

    public function testClear()
    {
        $request = Request::findOne(['id' => rand(1,3)]);
        $request->clearRequest();

        $product = RequestProduct::findAll(['request_id' => $request->id]);

        expect($product)->equals([]);
    }

    public function testCreateTransaction()
    {   
        $shopID = rand(5,7);
        $transactionID = $this->transactionService->createTransaction(['store_id' => $shopID]);

        $transaction = Operations::findOne(['id' => $transactionID]);

        expect($transaction->where)->equals($shopID);
        expect($transaction->type)->equals(2);
    }

    public function testSuccessFillTransaction()
    {   
        $shopID = rand(5,7);
        $transactionID = $this->transactionService->createTransaction(['store_id' => $shopID]);
        $productRequest = RequestProduct::find()->where(['request_id' => 1])->with('product')->asArray()->all();

        foreach($productRequest as $key => $onePosition){
            if($onePosition['product']['amount'] >= $onePosition['amount']){
                $arrProduct[$key][] = $onePosition['product_id'];
                $arrProduct[$key][] = ''; //vproduct_id

                $listProduct[$onePosition['product_id']] = $onePosition['product_id'];
            }
        }

        $fillTransaction = $this->transactionService->fillTransaction([
            'transaction_id' => $transactionID,
            'arr_product' => $arrProduct,
            'store_id' => $shopID,
            'request_id' => 1
        ]);
        
        $transaction = Operations::findOne(['transaction' => $fillTransaction['transaction']]);

        $totalUa = $totalUsd = $costPrice = $tradePrice = 0;

        foreach($transaction->getProducts()->asArray()->all() as $one){
            unset($listProduct[$one['product_id']]);

            $totalUa += ($one['price'] * $one['amount']);
            $costPrice += ($one['cost_price'] * $one['amount']);
            $tradePrice += ($one['trade_price'] * $one['amount']);
        }
        $totalUsd = ($totalUa / $transaction->course);

        expect($transaction->total_ua)->equals($totalUa);
        expect($transaction->total_usd)->equals($totalUsd);
        expect($transaction->cost_price)->equals($costPrice);
        expect($transaction->trade_price)->equals($tradePrice);
        expect($listProduct)->equals([]);
    }
}