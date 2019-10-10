<?php

namespace tests\unit;

use app\models\Characteristic;
use app\models\CharacteristicValue;
use app\tests\fixtures\CharacteristicFixture;
use app\tests\fixtures\CharacteristicValueFixture;
use Codeception\Test\Unit;

class CharacteristicValueTest extends Unit
{
    /**
     * @var \UnitTester
     */
    public $tester;

    /* подгрузка данных в тестовую бд,перед тестами */
    public function _before()
    {
        $this->tester->haveFixtures([
            'characteristic' => [
                'class' => CharacteristicFixture::className(),
            ],
            'characteristic-value' => [
                'class' => CharacteristicValueFixture::className(),
            ]
        ]);
    }

    public function _after()
    {
        \app\models\CharacteristicValue::deleteAll();
    }

    public function testCreateSuccess()
    {
        $char = $this->tester->grabFixture('characteristic', 1);
        $data = [
            'name' => 'yellow',
            'status' => $char->id,
        ];

        $model = new CharacteristicValue();
        $model->name = $data['name'];
        $model->id_char = $data['status'];

        $this->assertTrue($model->save());

        $charValue = CharacteristicValue::findOne($model->id);

        expect($charValue->name)->equals($data['name']);
        expect($charValue->id_char)->equals($char->id);
    }

    public function testCreateSimilarValue()
    {
        $char = $this->tester->grabFixture('characteristic', 1);
        $data = [
            'name' => 'red',
            'status' => $char->id,
        ];

        $model = new CharacteristicValue();
        $model->name = $data['name'];
        $model->id_char = $data['status'];

        $this->assertFalse($model->save());

        expect_that($model->getErrors('name'));
        expect($model->getFirstError('name'))
            ->equals('Имя уже существует');
    }

    public function testEmpty()
    {
        $data = [
            'name' => null,
            'status' => null,
        ];

        $model = new CharacteristicValue();
        $model->name = $data['name'];
        $model->id_char = $data['status'];

        $this->assertFalse($model->save());

        expect_that($model->getErrors('name'));
        expect($model->getFirstError('name'))
            ->equals('Значение характеристики cannot be blank.');

        expect_that($model->getErrors('id_char'));
    }

    public function testEdit()
    {
        $char = $this->tester->grabFixture('characteristic-value', 1);

        $data = [
            'name' => 'newValue',
            'status' => $char->id,
        ];

        $oldChar = clone $char;

        $char->name = $data['name'];
        $this->assertTrue($char->save());

        expect($char->name)->equals($data['name']);
        expect($char->name)->notEquals($oldChar->name);
    }
}