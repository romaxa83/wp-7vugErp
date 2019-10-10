<?php

namespace tests\unit\transaction;

use Yii;
use app\models\Operations;
use app\models\OperConsumption;
use app\models\Product;
use app\tests\fixtures\AgentFixture;
use app\tests\fixtures\ManyProductFixture;
use app\tests\fixtures\ManyConsumptionOperationFixture;
use app\tests\fixtures\CategoryFixture;
use Codeception\Test\Unit;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use app\controllers\LiveEditController;
use app\tests\helper\transaction\Transaction as TransactionHelper;
use app\tests\helper\transaction\TransactionCalculate as CalculateHelper;


class LiveEditConsumptionTest extends Unit
{
     /**
     * @var \UnitTester
     */
    public $tester;

    public $countTransaction = 5;
    public $countProduct = 5;

    private $countChange = 5;
    //heplers
    private $transactionHelper;
    private $transactionCalculateHelper;
    //контроллер для liveEdit
    private $liveEdit;
    /* подгрузка данных в тестовую бд */
    public function _before()
    {
        $this->transactionHelper = new TransactionHelper();
        $this->transactionCalculateHelper = new CalculateHelper();
        $this->liveEdit = new LiveEditController('live/edit',\yii\base\Module::className());
    }
    /* очистка данных из бд,после выполнения класса */
    public function testFreshData()
    {
        \app\models\Product::deleteAll();
        \app\models\Category::deleteAll();
        \app\models\OperComing::deleteAll();
        \app\models\OperConsumption::deleteAll();
        \app\models\Operations::deleteAll();

        $this->tester->haveFixtures([
            'product' => [
                'class' => ManyProductFixture::className(),
            ],
            'agent' => [
                'class' => AgentFixture::className(),
            ],
            'many-consumption-operation' => [
                'class' => ManyConsumptionOperationFixture::className(),
            ],
            'category' => [
                'class' => CategoryFixture::className(),
            ]
        ]);

        $startDataProduct = Product::find()->asArray()->where(['>','amount',($this->countTransaction * 10)])->limit(5)->indexBy('id')->all();
        Yii::$app->cache->set('startDataProduct',$startDataProduct);
    }
    /* проверка наполнения */
    public function testFillTransaction()
    {
        $resultatFilling  = $this->transactionHelper->fillTransaction($this->countTransaction,$this->countProduct,'consumption');
        expect($resultatFilling)->notEquals([]);

        foreach($resultatFilling as $resultatSaveProduct){
            $data = array_pop($resultatSaveProduct);
            $totalPrice = array_pop($resultatSaveProduct);

            foreach($resultatSaveProduct as $indexTransaction => $resultat){        
                $transaction = Yii::$app->db
                    ->createCommand("SELECT * FROM operations WHERE id = {$data['transaction_id']}")
                    ->queryOne();

                expect((double)$transaction['total_ua'])->equals((double)str_pad((string)$totalPrice['Ua'],14,0));
                expect((double)$transaction['total_usd'])->equals((double)str_pad((string)$totalPrice['Usd'],14,0));
                expect((double)$transaction['trade_price'])->equals((double)str_pad((string)$totalPrice['TradePrice'],14,0));
                foreach($resultat as $indexProduct => $oneResultat){
                    expect($oneResultat)->equals(true);

                    $product = $this->tester->grabFixture('product', $indexProduct);
                    $productDb = Yii::$app->db
                        ->createCommand("SELECT `cost_price`,`amount`,`id` FROM product WHERE id = {$indexProduct}")
                        ->queryOne();

                    expect($productDb['amount'])->equals($product['amount']);
                    expect($productDb['cost_price'])->equals($product['cost_price']);
                }
            }
        }
    }
    
    public function testSuccessAmountUp()
    {
        $arrayRow = $this->transactionHelper->getRandomRow('oper_consumption',$this->countProduct,implode(',',array_keys(Yii::$app->cache->get('startDataProduct'))));

        foreach($arrayRow['transactionRow'] as $oneRow){
            $data = [
                'typeLifeEdit' => 'edit-consumption',
                'productId' => $oneRow['product_id'],
                'variantId' => '',
                'transaction_id' => $oneRow['transaction_id'],
                'field' => 'amount',
                'value' => rand(10,50)
            ];

            $result = Json::decode($this->liveEdit->actionEntry($data));
            expect($result['status'])->equals('ok');

            $calculateData = $this->transactionCalculateHelper->calculateData('consumption',$oneRow['product_id']);
            expect($calculateData)->notEquals([]);

            $updateRow = Yii::$app->db
                ->createCommand("SELECT * FROM `oper_consumption` WHERE product_id = {$oneRow['product_id']} AND transaction_id = {$oneRow['transaction_id']}")
                ->queryOne();

            expect($updateRow['amount'])->equals($data['value']);
        }
        $this->compareProduct($oneRow['product_id'],end($calculateData));
    }

    public function testSuccessAmountDown()
    {
        $arrayRow = $this->transactionHelper->getRandomRow('oper_consumption',$this->countProduct,implode(',',array_keys(Yii::$app->cache->get('startDataProduct'))));

        foreach($arrayRow['transactionRow'] as $oneRow){
            $data = [
                'typeLifeEdit' => 'edit-consumption',
                'productId' => $oneRow['product_id'],
                'variantId' => '',
                'transaction_id' => $oneRow['transaction_id'],
                'field' => 'amount',
                'value' => rand(1,10)
            ];

            $result = Json::decode($this->liveEdit->actionEntry($data));
            expect($result['status'])->equals('ok');

            $calculateData = $this->transactionCalculateHelper->calculateData('consumption',$oneRow['product_id']);
            expect($calculateData)->notEquals([]);

            $updateRow = Yii::$app->db
                ->createCommand("SELECT * FROM `oper_consumption` WHERE product_id = {$oneRow['product_id']} AND transaction_id = {$oneRow['transaction_id']}")
                ->queryOne();

            expect($updateRow['amount'])->equals($data['value']);
        }
        $this->compareProduct($oneRow['product_id'],end($calculateData));
    }
    /* сравнения product строк */
    private function compareProduct(int $id,array $currentState)
    {
        $product = Yii::$app->db->createCommand("SELECT * FROM `product` WHERE id = {$id}")->queryOne();

        expect($product['amount'])->equals($currentState['amount']);
    }
}