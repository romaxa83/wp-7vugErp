<?php

namespace tests\unit\transaction;

use app\models\Agent;
use app\models\Operations;
use app\models\OperConsumption;
use app\models\Product;
use app\tests\fixtures\ManyProductFixture;
use app\tests\fixtures\OperationFixture;
use app\tests\fixtures\StoreFixture;
use app\tests\fixtures\CategoryFixture;
use Codeception\Test\Unit;


class ConsumptionTest extends Unit
{
    /**
     * @var \UnitTester
     */
    public $tester;
    /* подгрузка данных в тестовую бд,перед тестами */
    public function _before()
    {
        $this->tester->haveFixtures([
            'product' => [
                'class' => ManyProductFixture::className(),
            ],
            'store' => [
                'class' => StoreFixture::className(),
            ],
            'operation' => [
                'class' => OperationFixture::className(),
            ],
            'category' => [
                'class' => CategoryFixture::className(),
            ]
        ]);
    }
    /* очистка данных из бд,после выполнения класса */
    public function _after()
    {
        \app\models\Product::deleteAll();
        \app\models\Category::deleteAll();
        \app\models\OperComing::deleteAll();
        \app\models\OperConsumption::deleteAll();
        \app\models\Operations::deleteAll();
    }
    /* TRANSACTION */
    /* тест на внесения пустых значений */
    public function testCreateEmptyOperation()
    {
        $data = [
            'Operations' => [
                'whence' => null,
                'where' => null,
                'date' => null
            ]
        ];

        $model = new Operations();

        $this->assertTrue($model->load($data));
        $this->assertFalse($model->validate());
        $this->assertFalse($model->save());

        expect_that($model->getErrors('whence'));
        expect($model->getFirstError('whence'))->equals('Откуда cannot be blank.');

        expect_that($model->getErrors('where'));
        expect($model->getFirstError('where'))->equals('Куда cannot be blank.');

        expect_that($model->getErrors('date'));
        expect($model->getFirstError('date'))->equals('Дата cannot be blank.');
    }
    /* тест на внесения корректных значений */
    public function testCreateSuccessOperation()
    {
        $date = (new \DateTimeImmutable('now', new \DateTimeZone('Europe/Kiev')))->format("Y-m-d H:i:s");
        $store = $this->tester->grabFixture('store', 1);
        
        $data = [
            'Operations' => [
                'whence' => 1,
                'where' => $store->id,
                'date' => $date,
                'type' => 2
            ]
        ];

        $model = new \app\models\Operations();
        
        $this->assertTrue($model->load($data));
        $this->assertTrue($model->save());

        $transaction = Operations::findOne(['id' => $model->id]);

        expect($transaction->whence)->equals($data['Operations']['whence']);
        expect($transaction->where)->equals($data['Operations']['where']);
        expect($transaction->status)->equals(Operations::OPERATION_EMPTY);
        expect($transaction->course)->equals(26);
        expect($transaction->type)->equals(2);
        expect($transaction->date)->equals($date);

        expect($transaction->transaction)->notNull();

        expect($transaction->old_value)->null();
        expect($transaction->prod_value)->null();

        expect($transaction->total_usd)->null();
        expect($transaction->total_ua)->null();
        expect($transaction->trade_price)->null();
        expect($transaction->start_price)->null();
        expect($transaction->cost_price)->null();
        expect($transaction->recalculated)->null();
    }
    /* PRODUCT */
    /* тест на внесения пустых значений в товар транзакций */
    public function testAddEmptyProduct()
    {
        $data = $this->generateOperConsumptionData(null,null,null,null);

        $model = new OperConsumption();

        $this->assertTrue($model->load($data));
        $this->assertFalse($model->validate());
        $this->assertFalse($model->save());

        expect_that($model->getErrors('product_id'));
        expect($model->getFirstError('product_id'))->equals('Продукт cannot be blank.');

        expect_that($model->getErrors('amount'));
        expect($model->getFirstError('amount'))->equals('Кол-во cannot be blank.');

        expect_that($model->getErrors('price'));
        expect($model->getFirstError('price'))->equals('Цена продажи cannot be blank.');
    }
    /* тест на добавления одного товара со статичными значениями в транзакцию */
    public function testAddOneStaticProduct()
    {
        $product = $this->tester->grabFixture('product', 3);
        $transaction = $this->tester->grabFixture('operation', 'emptyConsumption');

        $data = $this->generateOperConsumptionData(
            $product->id,
            10,
            10,
            $transaction->id
        );

        $model = new OperConsumption();

        $this->assertTrue($model->load($data));
        
        if($product->amount >= $data['OperConsumption']['amount']){
            //остатка достаточно для расхода. Ожидаем добавления товара
            $this->assertTrue($model->validate());
            $this->assertTrue($model->save());

            $operConsumption = OperConsumption::findOne($model->id);

            expect($operConsumption->transaction_id)->equals($data['OperConsumption']['transaction_id']);
            expect($operConsumption->product_id)->equals($data['OperConsumption']['product_id']);
            expect($operConsumption->amount)->equals($data['OperConsumption']['amount']);
            expect($operConsumption->price)->equals($data['OperConsumption']['price']);
            expect($operConsumption->vproduct_id)->null();

            $productUpdate = Product::findOne($product->id);

            expect($operConsumption->trade_price)->equals($productUpdate->trade_price);
            expect($operConsumption->cost_price)->equals($productUpdate->cost_price);
            expect($productUpdate->amount)->equals((int)$product->amount - (int)$data['OperConsumption']['amount']);

            $transactionUpdate = Operations::findOne($transaction->id);

            expect($transactionUpdate->transaction)->equals($transaction->transaction);
            expect($transactionUpdate->date_update)->notEquals($transaction->date_update);

            expect($transactionUpdate->total_ua)->equals($operConsumption->price * $operConsumption->amount);
            expect($transactionUpdate->total_usd)->equals($transactionUpdate->total_ua / $transactionUpdate->course);
            
            expect($transactionUpdate->start_price)->null();
            
            expect($transactionUpdate->trade_price)->equals($operConsumption->trade_price * $operConsumption->amount);
            expect($transactionUpdate->cost_price)->equals($operConsumption->cost_price * $operConsumption->amount);
        }else{
            //остатка не достаточно для расхода. Ожидаем что бы товар не был добавлен
            $this->assertFalse($model->validate());
        }
    }
    /* тест на добавления товаров с динамичными значениями в транзакцию */
    public function testAddManyDynamicProduct()
    {
        $transaction = $this->tester->grabFixture('operation', 'emptyConsumption');

        $totalUa = 0;
        $tradePrice = 0;
        $costPrice = 0;
        $i = 1;
        
        while($i < 20){
            $product = $this->tester->grabFixture('product', $i);

            $data = $this->generateOperConsumptionData(
                $product->id,
                rand(0, 100),
                floatVal(rand(1, 200) . '.' . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9)),
                $transaction->id
            );
            
            $model = new OperConsumption();

            $this->assertTrue($model->load($data));

            if($product->amount >= $data['OperConsumption']['amount']){
                //остатка достаточно для расхода. Ожидаем добавления товара
                $this->assertTrue($model->validate());
                $this->assertTrue($model->save());

                $operConsumption = OperConsumption::findOne($model->id);
                
                $totalUa += ($operConsumption->price * $operConsumption->amount);
                $costPrice += ($operConsumption->cost_price * $operConsumption->amount);
                $tradePrice += ($operConsumption->trade_price * $operConsumption->amount);
                
                $i++;
            }else{
                //остатка не достаточно для расхода. Ожидаем что бы товар не был добавлен
                $this->assertFalse($model->validate());
            }
        }
        $totalUsd = ($totalUa / $transaction->course);
        //проверяем кол-во записей
        $count = OperConsumption::find()->count();
        expect($count)->equals($i - 1);

        $updateTransaction = Operations::findOne($transaction->id);
        
        expect((double)$updateTransaction->total_ua)->equals((double)str_pad((string)$totalUa,14,0));
        expect((double)$updateTransaction->total_usd)->equals((double)str_pad((string)$totalUsd,14,0));
        expect((double)$updateTransaction->cost_price)->equals((double)str_pad((string)$costPrice,14,0));
        expect((double)$updateTransaction->trade_price)->equals((double)str_pad((string)$tradePrice,14,0));
    }
    /* преобразовния массива к структуре готовой для загрузки в модель */
    private function generateOperConsumptionData(
        $productId,
        $amount,
        $price,
        $transactionId
    )
    {
        return [
            'OperConsumption' => [
                'product_id' => $productId,
                'amount' => $amount,
                'price' => $price,
                'transaction_id' => $transactionId
            ]
        ];
    }
}