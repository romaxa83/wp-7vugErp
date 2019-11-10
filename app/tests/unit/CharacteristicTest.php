<?php

namespace tests\unit;

use app\models\Characteristic;
use app\models\CharacteristicValue;
use app\tests\fixtures\CharacteristicFixture;
use Codeception\Test\Unit;

class CharacteristicTest extends Unit
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
            ]
        ]);
    }

    public function testCreateSuccess()
    {
        $data = [
           'Characteristic' => [
               'name' => 'test'
           ]
        ];

        $model = new Characteristic();

        $this->assertTrue($model->load($data));
        $this->assertTrue($model->save());

        $char = Characteristic::findOne($model->id);

        expect($char->name)->equals($data['Characteristic']['name']);
    }

    public function testEmpty()
    {
        $data = [
            'Characteristic' => [
                'name' => null
            ]
        ];

        $model = new Characteristic();

        $this->assertTrue($model->load($data));
        $this->assertFalse($model->validate());
        $this->assertFalse($model->save());

        expect_that($model->getErrors('name'));
        expect($model->getFirstError('name'))
            ->equals('Название характеристики cannot be blank.');
    }

    public function testEdit()
    {
        $char = $this->tester->grabFixture('characteristic', 1);

        $data = [
            'Characteristic' => [
                'name' => 'updateChar'
            ]
        ];

        $oldChar = clone $char;

        $this->assertTrue($char->load($data));
        $this->assertTrue($char->save());

        expect($char->name)->equals($data['Characteristic']['name']);
        expect($char->name)->notEquals($oldChar->name);
    }

    public function testGetValue()
    {
        $char = $this->tester->grabFixture('characteristic', 1);

        expect($char->characteristicValues)->notNull();
        expect_that($char->characteristicValues[0] instanceof CharacteristicValue);
    }
}