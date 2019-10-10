<?php

namespace tests\unit\transaction;

use Yii;
use app\models\Operations;
use app\models\OperComing;
use app\models\Product;
use app\tests\fixtures\AgentFixture;
use app\tests\fixtures\ManyProductFixture;
use app\tests\fixtures\ManyComingOperationFixture;
use app\tests\fixtures\CategoryFixture;
use Codeception\Test\Unit;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use app\controllers\LiveEditController;
use app\tests\helper\transaction\Transaction as TransactionHelper;
use app\tests\helper\transaction\TransactionCalculate as CalculateHelper;


class LiveEditComingTest extends Unit
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
        \app\models\OperConsumption::deleteAll();
        \app\models\OperComing::deleteAll();
        \app\models\Operations::deleteAll();

        $this->tester->haveFixtures([
            'product' => [
                'class' => ManyProductFixture::className(),
            ],
            'agent' => [
                'class' => AgentFixture::className(),
            ],
            'many-coming-operation' => [
                'class' => ManyComingOperationFixture::className(),
            ],
            'category' => [
                'class' => CategoryFixture::className(),
            ]
        ]);

        $startDataProduct = Product::find()->asArray()->where(['in','id',[1,2,3,4,5]])->indexBy('id')->all();
        Yii::$app->cache->set('startDataProduct',$startDataProduct);
    }
    /* проверка наполнения */
    public function testFillTransaction()
    {
        $resultatFilling  = $this->transactionHelper->fillTransaction($this->countTransaction,$this->countProduct,'coming');
        expect($resultatFilling)->notEquals([]);

        foreach($resultatFilling as $resultatSaveProduct){
            $data = array_pop($resultatSaveProduct);
            $totalPrice = array_pop($resultatSaveProduct);

            foreach($resultatSaveProduct as $indexTransaction => $resultat){        
                $transaction = Yii::$app->db
                    ->createCommand("SELECT * FROM operations WHERE id = {$indexTransaction}")
                    ->queryOne();

                expect((double)$transaction['total_ua'])->equals((double)str_pad((string)$totalPrice['Ua'],14,0));
                expect((double)$transaction['total_usd'])->equals((double)str_pad((string)$totalPrice['Usd'],14,0));
                expect((double)$transaction['cost_price'])->equals((double)str_pad((string)$totalPrice['CostPrice'],14,0));
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
    /* LIVE_EDIT */
    /* изменения колличества позиций тарназакций в меньшую сторону */
    public function testSuccessAmountDown()
    {
        $arrayRow = $this->transactionHelper->getRandomRow('oper_coming',$this->countTransaction,$this->countProduct);

        foreach($arrayRow['transactionRow'] as $oneRow){
            $data = [
                'typeLifeEdit' => 'edit-coming',
                'productId' => $oneRow['product_id'],
                'variantId' => '',
                'transaction_id' => $oneRow['transaction_id'],
                'field' => 'amount',
                'value' => rand(1,20)
            ];

            $result = Json::decode($this->liveEdit->actionEntry($data));
            expect($result['status'])->equals('ok');

            $calculateData = $this->transactionCalculateHelper->calculateData('coming',$oneRow['product_id']);
            expect($calculateData)->notEquals([]);

            $updateRow = Yii::$app->db
                ->createCommand("SELECT * FROM `oper_coming` WHERE product_id = {$oneRow['product_id']} AND transaction_id = {$oneRow['transaction_id']}")
                ->queryOne();

            $this->compareRow($updateRow,$calculateData[$oneRow['transaction_id']]);
        }
        $this->compareProduct($oneRow['product_id'],end($calculateData));
    }
    /* изменения колличества позиций тарназакций в 0 */
    public function testSuccessAmountToZero()
    {
        $arrayRow = $this->transactionHelper->getRandomRow('oper_coming',$this->countChange);

        foreach($arrayRow['transactionRow'] as $key => $oneRow){
            $data = [
                'typeLifeEdit' => 'edit-coming',
                'productId' => $oneRow['product_id'],
                'variantId' => '',
                'transaction_id' => $oneRow['transaction_id'],
                'field' => 'amount',
                'value' => 0
            ];

            $result = Json::decode($this->liveEdit->actionEntry($data));
            expect($result['status'])->equals('ok');

            $calculateData = $this->transactionCalculateHelper->calculateData('coming',$oneRow['product_id']);
            expect($calculateData)->notEquals([]);

            $updateRow = Yii::$app->db
                ->createCommand("SELECT * FROM `oper_coming` WHERE product_id = {$oneRow['product_id']} AND transaction_id = {$oneRow['transaction_id']}")
                ->queryOne();

            $this->compareRow($updateRow,$calculateData[$oneRow['transaction_id']]);
        }
        $this->compareProduct($oneRow['product_id'],end($calculateData));
    }
    /* изменения колличества позиций тарназакций в большую сторону */
    public function testSuccessAmountUp()
    {
        $arrayRow = $this->transactionHelper->getRandomRow('oper_coming',$this->countChange);

        foreach($arrayRow['transactionRow'] as $oneRow){
            $data = [
                'typeLifeEdit' => 'edit-coming',
                'productId' => $oneRow['product_id'],
                'variantId' => '',
                'transaction_id' => $oneRow['transaction_id'],
                'field' => 'amount',
                'value' => rand(1,20)
            ];

            $result = Json::decode($this->liveEdit->actionEntry($data));
            expect($result['status'])->equals('ok');

            $calculateData = $this->transactionCalculateHelper->calculateData('coming',$oneRow['product_id']);
            expect($calculateData)->notEquals([]);

            $updateRow = Yii::$app->db
                ->createCommand("SELECT * FROM `oper_coming` WHERE product_id = {$oneRow['product_id']} AND transaction_id = {$oneRow['transaction_id']}")
                ->queryOne();

            $this->compareRow($updateRow,$calculateData[$oneRow['transaction_id']]);
        }
        $this->compareProduct($oneRow['product_id'],end($calculateData));
    }
    /* изменения цены прихода */
    public function testSuccessStartPrice()
    {
        $arrayRow = $this->transactionHelper->getRandomRow('oper_coming',$this->countTransaction,$this->countProduct);

        foreach($arrayRow['transactionRow'] as $oneRow){
            $data = [
                'typeLifeEdit' => 'edit-coming',
                'productId' => $oneRow['product_id'],
                'variantId' => '',
                'transaction_id' => $oneRow['transaction_id'],
                'field' => 'start_price',
                'value' => floatVal(rand(1, 200) . '.' . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9))
            ];

            $result = Json::decode($this->liveEdit->actionEntry($data));
            expect($result['status'])->equals('ok');

            $calculateData = $this->transactionCalculateHelper->calculateData('coming',$oneRow['product_id']);
            expect($calculateData)->notEquals([]);

            $updateRow = Yii::$app->db
                ->createCommand("SELECT * FROM `oper_coming` WHERE product_id = {$oneRow['product_id']} AND transaction_id = {$oneRow['transaction_id']}")
                ->queryOne();

            $this->compareRow($updateRow,$calculateData[$oneRow['transaction_id']]);
        }
        $this->compareProduct($oneRow['product_id'],end($calculateData));
    }
    /* сравнения oper_coming строк */
    private function compareRow(array $updateRow,array $currentState)
    {
        expect($updateRow['amount'])->equals($currentState['amount'] - $updateRow['old_amount']);
        if($updateRow['amount'] > 0){
            expect($updateRow['cost_price'])->equals($currentState['cost_price']);
        }
        expect($updateRow['old_amount'])->equals($currentState['amount'] - $updateRow['amount']);
        expect($updateRow['old_cost_price'])->equals($currentState['old_cost_price']);
    }
    /* сравнения product строк */
    private function compareProduct(int $id,array $currentState)
    {
        $product = Yii::$app->db->createCommand("SELECT * FROM `product` WHERE id = {$id}")->queryOne();

        expect($product['amount'])->equals($currentState['amount']);

        expect($product['cost_price'])->equals($currentState['cost_price']);
        expect($product['start_price'])->equals($currentState['start_price']);
        expect($product['trade_price'])->equals($currentState['trade_price']);
    }
}