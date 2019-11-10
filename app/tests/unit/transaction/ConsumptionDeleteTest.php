<?php

namespace tests\unit\transaction;

use Yii;
use app\models\Operations;
use app\models\OperComing;
use app\models\Product;
use app\tests\fixtures\AgentFixture;
use app\tests\fixtures\ManyProductFixture;
use app\tests\fixtures\ManyConsumptionOperationFixture;
use app\tests\fixtures\CategoryFixture;
use Codeception\Test\Unit;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use app\controllers\OperationConsumptionController;
use app\tests\helper\transaction\Transaction as TransactionHelper;
use app\tests\helper\transaction\TransactionCalculate as CalculateHelper;


class ConsumptionDeleteTest extends Unit
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
    /* подгрузка данных в тестовую бд */
    public function _before()
    {
        $this->transactionHelper = new TransactionHelper();
        $this->transactionCalculateHelper = new CalculateHelper();
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
        $resultatFilling  = $this->transactionHelper->fillTransaction($this->countTransaction,1,'consumption');
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
    /* удаления продукта */
    public function testSuccessDeleteProduct()
    {
        $arrayRow = $this->transactionHelper->getRandomRow('oper_consumption',5,implode(',',array_keys(Yii::$app->cache->get('startDataProduct'))));

        foreach($arrayRow['transactionRow'] as $oneRow){
            OperationConsumptionController::actionDeleteProduct([
                'base' => $oneRow['product_id'],
                'variant' => '',
                'transaction' => $oneRow['transaction_id']
            ]);

            $calculateData = $this->transactionCalculateHelper->calculateData('delete-product-consumption',$oneRow['product_id']);

            $updateRow = Yii::$app->db
                ->createCommand("SELECT * FROM `oper_consumption` WHERE product_id = {$oneRow['product_id']}")
                ->queryAll();

            foreach($updateRow as $oneUpdateRow){
                $currentState = $calculateData[$oneUpdateRow['transaction_id']];

                expect($oneUpdateRow['amount'])->equals($currentState['old_amount'] - $currentState['amount']);
            }
        }
        $product = Yii::$app->db->createCommand("SELECT * FROM `product` WHERE id = {$calculateData['id']}")->queryOne();

        expect($product['amount'])->equals($calculateData['amount']);
        expect($product['cost_price'])->equals($calculateData['cost_price']);
        expect($product['trade_price'])->equals($calculateData['trade_price']);

        Yii::$app->cache->set('keyTransactionComing',ArrayHelper::getColumn($arrayRow['transactionRow'],'transaction_id'));
    }
    /* удаления транзакций без товаров */
    public function testSuccessDeleteTransaction()
    {
        foreach(Yii::$app->cache->get('keyTransactionComing') as $oneTransactionId){
            $operation = Operations::findOne($oneTransactionId);
            $this->assertTrue((boolean)$operation->delete());
        }
    }
    /* удаления транзакций с товарами */
    public function testErrorDeleteTransaction()
    {
        $resultatFilling  = $this->transactionHelper->fillTransaction($this->countTransaction,1,'consumption');
        expect($resultatFilling)->notEquals([]);

        foreach($resultatFilling as $oneTransactionResultat){
            $arrayKey = array_keys($oneTransactionResultat);
            $operation = Operations::findOne($arrayKey[0]);
            $this->expectException(yii\db\IntegrityException::class);
            $operation->delete();
        }
    }
}