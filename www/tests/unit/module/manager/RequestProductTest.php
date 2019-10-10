<?php namespace module\manager\model;

use app\modules\manager\models\Request;
use app\modules\manager\models\RequestProduct;
use app\tests\fixtures\AgentFixture;
use app\tests\fixtures\ManyProductFixture;
use app\tests\fixtures\CategoryFixture;
use app\tests\fixtures\StoreFixture;
use app\tests\fixtures\RequestFixture;
use app\models\Agent;

class RequestProductTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
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
            ]
        ]);
    }

    public function testAddEmptyProduct()
    {
        $model = new RequestProduct(['scenario' => RequestProduct::NEW_ROW]);

        $data = [
            'request_id' => '',
            'product_id' => '',
            'amount' => '',
            'price' => '',
            'cost_price' => '',
            'trade_price' => '',
        ];

        $model->load(['RequestProduct' => $data]);

        $this->assertFalse($model->validate());
        $resultat = $model->saveProduct(rand(1,2));

        expect($resultat['status'])->equals(RequestProduct::ERROR_ADD);

        expect_that($model->getErrors('product_id'));
        expect($model->getFirstError('product_id'))->equals('Продукт не выбран');

        expect_that($model->getErrors('request_id'));
        expect($model->getFirstError('request_id'))->equals('Request Id cannot be blank.');
    }
    // tests
    public function testSuccessAddProduct()
    {
        $model = new RequestProduct(['scenario' => RequestProduct::NEW_ROW]);

        $request = $this->tester->grabFixture('request', 1);
        $store = Agent::find()->where(['id' => $request->store_id])->asArray()->one();

        $data = [
            'request_id' => 1,
            'product_id' => 1,
            'amount' => 5,
            'price' => '',
            'cost_price' => '',
            'trade_price' => '',
        ];

        $model->load(['RequestProduct' => $data]);

        $this->assertFalse($model->validate());
        $resultat = $model->saveProduct($store['price_type']);

        $updateRow = Request::find()->asArray()->where(['id' => $request->id])->one();

        expect($resultat['status'])->equals(RequestProduct::SUCCESS_ADD);
        expect($updateRow['status'])->equals(Request::REQUEST_NOT_EMPTY);

        expect($resultat['model']->amount)->equals($data['amount']);
        expect($resultat['model']->request_id)->equals($data['request_id']);
        expect($resultat['model']->product_id)->equals($data['product_id']);
        

        $product = $this->tester->grabFixture('product', 1);

        expect($resultat['model']->cost_price)->equals($product['cost_price']);
        expect($resultat['model']->trade_price)->equals($product['trade_price']);
        expect($resultat['model']->price)->equals($product['price' . $store['price_type']]);
    }
    // tests
    public function testDuplicateAddProduct()
    {
        $model = new RequestProduct(['scenario' => RequestProduct::NEW_ROW]);

        $data = [
            'request_id' => 1,
            'product_id' => 1,
            'amount' => 5,
            'price' => '',
            'cost_price' => '',
            'trade_price' => '',
        ];

        $model->load(['RequestProduct' => $data]);

        $this->assertFalse($model->validate());
        $resultat = $model->saveProduct(rand(1,2));
        expect($resultat['status'])->equals(RequestProduct::DUPLICATE_ADD);
    }

    public function testChangeAmountToNegative()
    {
        $model = RequestProduct::find()->where(['request_id' => 1])->asArray()->one();
        $amount = (int)('-' . rand(1,100));

        $resultat = RequestProduct::ChangeAmount(1,1,$amount);
        $this->assertFalse($resultat['status']);

        $updateRow = RequestProduct::find()->where(['request_id' => 1])->asArray()->one();

        expect($updateRow['amount'])->notEquals($amount);
    }

    public function testChangeAmountToPositive()
    {
        $model = RequestProduct::find()->where(['request_id' => 1])->asArray()->one();
        $amount = rand(1,100);

        $resultat = RequestProduct::ChangeAmount(1,1,$amount);
        $this->assertTrue($resultat['status']);

        expect($resultat['model']->amount)->equals($amount);
    }
}