<?php

namespace tests\unit\transaction;

use app\models\Operations;
use app\models\OperComing;
use app\models\Product;
use app\tests\fixtures\AgentFixture;
use app\tests\fixtures\ManyProductFixture;
use app\tests\fixtures\OperationFixture;
use app\tests\fixtures\CategoryFixture;
use app\tests\fixtures\ProductFixture;
use Codeception\Test\Unit;


class ComingTest extends Unit
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
            'agent' => [
                'class' => AgentFixture::className(),
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
        $date = (new \DateTimeImmutable('now',new \DateTimeZone('Europe/Kiev')))->format("Y-m-d H:i:s");
        $agent = $this->tester->grabFixture('agent', 1);
        
        $data = [
            'Operations' => [
                'whence' => $agent->id,
                'where' => 1,
                'date' => $date,
                'type' => 1
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
        expect($transaction->type)->equals(1);
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
        $data = $this->generateOperComingData(null,null,null,null,null,null,null);

        $model = new OperComing();

        $this->assertTrue($model->load($data));
        $this->assertFalse($model->validate());
        $this->assertFalse($model->save());

        expect_that($model->getErrors('product_id'));
        expect($model->getFirstError('product_id'))->equals('Продукт cannot be blank.');

        expect_that($model->getErrors('amount'));
        expect($model->getFirstError('amount'))->equals('Количество cannot be blank.');

        expect_that($model->getErrors('start_price'));
        expect($model->getFirstError('start_price'))->equals('Цена прихода cannot be blank.');

        expect_that($model->getErrors('price1'));
        expect($model->getFirstError('price1'))->equals('Цена 1 cannot be blank.');

        expect_that($model->getErrors('price2'));
        expect($model->getFirstError('price2'))->equals('Цена 2 cannot be blank.');

        expect_that($model->getErrors('transaction_id'));
        expect($model->getFirstError('transaction_id'))->equals('Транзакция cannot be blank.');
    }    
    /* тест на добавления одного товара со статичными значениями в транзакцию */
    public function testAddOneStaticProduct()
    {
        $product = $this->tester->grabFixture('product', 1);
        $transaction = $this->tester->grabFixture('operation', 'emptyComing');

        $data = [
            'OperComing' => [
                'product_id' => $product->id,
                'amount' => 100,
                'start_price_ua' => 0.000,
                'start_price' => 20,
                'price1' => 25,
                'price2' => 25,
                'transaction_id' => $transaction->id
            ]
        ];

        $model = new OperComing();

        $this->assertTrue($model->load($data));
        $this->assertTrue($model->validate());
        $this->assertTrue($model->save());

        $operComing = OperComing::findOne($model->id);
        //получаем новые цены 
        $newCost =  (($operComing->old_cost_price * $operComing->old_amount) + ($operComing->start_price * $operComing->amount))/($operComing->old_amount + $operComing->amount);
        $newTrade = ($newCost * (1 + getPerTradePrice()/100));
        
        expect($operComing->transaction_id)->equals($data['OperComing']['transaction_id']);
        expect($operComing->product_id)->equals($data['OperComing']['product_id']);

        expect($operComing->vproduct_id)->null();
        
        expect($operComing->amount)->equals($data['OperComing']['amount']);
        
        expect($operComing->price1)->equals($data['OperComing']['price1']);
        expect($operComing->price2)->equals($data['OperComing']['price2']);
        
        expect($operComing->start_price)->equals($data['OperComing']['start_price']);
        expect($operComing->cost_price)->equals((double)$newCost);
        
        //проверяем запись данных до создания продукта транзакций
        expect($operComing->old_amount)->equals($product->amount);
        expect($operComing->old_cost_price)->equals($product->cost_price);

        //проверяем изменения в товаре после прихода (первого)
        $productUpdate = Product::findOne($product->id);

        expect($productUpdate->amount)->notEquals($product->amount);
        expect($productUpdate->amount)->equals((int)$product->amount + (int)$data['OperComing']['amount']);

        expect($productUpdate->start_price)->notEquals($product->start_price);
        expect($productUpdate->start_price)->equals($data['OperComing']['start_price']);

        expect($productUpdate->cost_price)->notEquals($product->cost_price);
        expect($productUpdate->cost_price)->equals((double)$newCost);

        expect($productUpdate->trade_price)->notEquals($product->trade_price);
        expect($productUpdate->trade_price)->equals((double)$newTrade);

        expect($productUpdate->price1)->equals($data['OperComing']['price1']);
        expect($productUpdate->price2)->equals($data['OperComing']['price2']);

        expect($productUpdate->change_price)->notEquals($product->change_price);
        
        //проверяем добавления в транзакцию
        $transactionUpdate = Operations::findOne($transaction->id);

        expect($transactionUpdate->transaction)->equals($transaction->transaction);
        expect($transactionUpdate->date_update)->notEquals($transaction->date_update);

        expect($transactionUpdate->total_usd)->notEquals($transaction->total_usd);
        expect($transactionUpdate->total_usd)->equals($data['OperComing']['amount'] * $data['OperComing']['start_price']);

        expect($transactionUpdate->start_price)->notEquals($transaction->start_price);
        expect($transactionUpdate->start_price)->equals($data['OperComing']['amount'] * $data['OperComing']['start_price']);

        expect($transactionUpdate->cost_price)->notEquals($transaction->cost_price);
        expect($transactionUpdate->cost_price)->equals($data['OperComing']['amount'] * $productUpdate->cost_price);
    }
    /* тест на добавления товаров с динамичными значениями в транзакцию */
    public function testAddManyDynamicProduct()
    {
        $transaction = $this->tester->grabFixture('operation', 'emptyComing');

        $totalUsd = 0;
        $costPrice = 0;

        for ($i = 1;$i < 100;$i++){
            $product = $this->tester->grabFixture('product', $i);

            $data = $this->generateOperComingData(
                $product->id,
                $amount = rand(1,100),
                0.000,
                floatVal(rand(1, 200) . '.' . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9)),
                rand(10, 100),
                rand(10, 100),
                $transaction->id
            );

            $model = new OperComing();

            $this->assertTrue($model->load($data));
            $this->assertTrue($model->validate());
            $this->assertTrue($model->save());
            
            $rowUpdate = OperComing::findOne(['id' => $model->id]);
            
            $totalUsd += ($amount * $rowUpdate->start_price);
            $costPrice += ($amount * $rowUpdate->cost_price);
        }
        $totalUa = ($totalUsd * $transaction->course);
        //проверяем кол-во записей
        $count = OperComing::find()->count();
        expect($count)->equals($i - 1);

        $transactionUpdate = Operations::findOne($transaction->id);

        expect((double)$transactionUpdate->total_ua)->equals((double)str_pad((string)$totalUa,14,0));
        expect((double)$transactionUpdate->total_usd)->equals((double)str_pad((string)$totalUsd,14,0));
        expect((double)$transactionUpdate->cost_price)->equals((double)str_pad((string)$costPrice,14,0));
    }
    /* преобразовния массива готового для загрузки в модель */
    private function generateOperComingData(
        $productId,
        $amount,
        $startPriceUa,
        $startPrice,
        $price1,
        $price2,
        $transactionId
    )
    {
        return [
            'OperComing' => [
                'product_id' => $productId,
                'amount' => $amount,
                'start_price_ua' => $startPriceUa,
                'start_price' => $startPrice,
                'price1' => $price1,
                'price2' => $price2,
                'transaction_id' => $transactionId
            ]
        ];
    }
}