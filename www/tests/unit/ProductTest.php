<?php

namespace tests\unit;

use app\models\Product;
use app\tests\fixtures\AgentFixture;
use app\tests\fixtures\CategoryFixture;
use app\tests\fixtures\ProductFixture;
use Codeception\Test\Unit;


class ProductTest extends Unit
{
    /**
     * @var \UnitTester
     */
    public $tester;

    /* подгрузка данных в тестовую бд,перед тестами */
    public function _before()
    {
        $this->tester->haveFixtures([
            'category' => [
                'class' => CategoryFixture::className(),
            ],
            'agent' => [
                'class' => AgentFixture::className(),
            ],
            'product' => [
                'class' => ProductFixture::className(),
            ]
        ]);
    }

    public function _after()
    {
        \app\models\Product::deleteAll();
    }

    public function testCreateSuccess()
    {
        $category = $this->tester->grabFixture('category', 1);
        $agent = $this->tester->grabFixture('agent', 1);
        //тестовые данные
        $data = [
            'Product' => [
                'vendor_code' => 000002002,
                'is_variant' => 1,
                'status' => 1,
                'name' => 'wood',
                'category_id' => $category->id,
                'agent_id' => $agent->id,
                'min_amount' => 2,
                'unit' => 'шт.',
                'margin'=> 20,
                'price1'=> 10,
                'price2'=> 20
            ]
        ];

        $model = new \app\models\Product();
        //проверка загрузки и сохранения
        $this->assertTrue($model->load($data));
        $this->assertTrue($model->save());
        //получаем сохраненый продукт и проверяем данные

        /** @var $product Product */
        $product = Product::findOne(['id' => $model->id]);

        expect($product->name)->equals($data['Product']['name']);
        expect($product->unit)->equals($data['Product']['unit']);
        expect($product->is_variant)->equals($data['Product']['is_variant']);
        expect($product->status)->equals($data['Product']['status']);
        expect($product->min_amount)->equals($data['Product']['min_amount']);
        expect($product->margin)->equals($data['Product']['margin']);
        expect($product->price1)->equals($data['Product']['price1']);
        expect($product->price2)->equals($data['Product']['price2']);
        expect($product->start_price)->equals(0);
        expect($product->cost_price)->equals(0);
        expect($product->trade_price)->equals(0);
        expect($product->created_at)->notNull();
        expect($product->updated_at)->notNull();
        expect($product->publish_status)->equals(0);
        //связи
        expect($product->category->name)->equals($category->name);
        expect($product->agent->firm)->equals($agent->firm);
    }

    public function testEmpty()
    {
        $data = [
            'Product' => [
                'vendor_code' => null,
                'is_variant' => null,
                'status' => null,
                'name' => null,
                'category_id' => null,
                'agent_id' => null,
                'min_amount' => null,
                'unit' => null,
                'margin'=> null,
                'price1'=> null,
                'price2'=> null
            ]
        ];

        $model = new \app\models\Product();

        $this->assertTrue($model->load($data));
        $this->assertFalse($model->validate());

        expect_that($model->getErrors('price1'));
        expect($model->getFirstError('price1'))
            ->equals('Цена 1 не была заполненная');

        expect_that($model->getErrors('price2'));
        expect($model->getFirstError('price2'))
            ->equals('Цена 2 не была заполненная');

        expect_that($model->getErrors('category_id'));
        expect($model->getFirstError('category_id'))
            ->equals('Категория товара не заполненная');

        expect_that($model->getErrors('agent_id'));
        expect($model->getFirstError('agent_id'))
            ->equals('Поставщик товара не заполнен');

        expect_that($model->getErrors('name'));
        expect($model->getFirstError('name'))
            ->equals('Наименование товара не заполнено');
    }

    public function testEdit()
    {
        $product = $this->tester->grabFixture('product', 1);

        $category = $this->tester->grabFixture('category', 1);
        $agent = $this->tester->grabFixture('agent', 2);

        $data = [
            'Product' => [
                'vendor_code' => $product->id . '00' . $category->id . '00' .$agent->id ,
                'status' => 1,
                'name' => 'product_update',
                'category_id' => $category->id,
                'agent_id' => $agent->id,
                'min_amount' => 2,
                'unit' => 'шт.',
                'margin'=> 100,
                'price1'=> 100,
                'price2'=> 100
            ]
        ];

        $oldProduct = clone $product;

        $this->assertTrue($product->load($data));
        $this->assertTrue($product->save());

        expect($product->price1)->equals($data['Product']['price1']);
        expect($product->price1)->notEquals($oldProduct->price1);

        expect($product->price2)->equals($data['Product']['price2']);
        expect($product->price2)->notEquals($oldProduct->price2);

        expect($product->name)->equals($data['Product']['name']);
        expect($product->name)->notEquals($oldProduct->name);

        expect($product->category_id)->equals($data['Product']['category_id']);
        expect($product->category_id)->notEquals($oldProduct->category_id);

        expect($product->agent_id)->equals($data['Product']['agent_id']);
        expect($product->agent_id)->notEquals($oldProduct->agent_id);

        expect($product->vendor_code)->equals($data['Product']['vendor_code']);
        expect($product->vendor_code)->notEquals($oldProduct->vendor_code);

        expect($product->margin)->equals($data['Product']['margin']);
        expect($product->margin)->notEquals($oldProduct->margin);

        expect($product->min_amount)->equals($data['Product']['min_amount']);
        expect($product->min_amount)->notEquals($oldProduct->min_amount);
    }
}