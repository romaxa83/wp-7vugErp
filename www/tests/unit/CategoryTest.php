<?php

namespace tests\unit;

use app\models\Category;
use app\tests\fixtures\CategoryFixture;
use app\tests\fixtures\CharacteristicFixture;
use Codeception\Test\Unit;
use PHPUnit\Framework\TestResult;

class CategoryTest extends Unit
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
            'characteristic' => [
                'class' => CharacteristicFixture::className(),
            ]
        ]);
    }

    public function _after()
    {
        \app\modules\manager\models\RequestProduct::deleteAll();
        \app\models\Product::deleteAll();
        \app\models\Category::deleteAll();
        \app\models\Characteristic::deleteAll();
    }

    public function testCreateSuccess()
    {
        $data = [
            'Category' => [
                'parent_id' => null,
                'name' => 'category',
                'position' => 1,
                'status' => 'on',
                'charsName' => null
            ]
        ];

        $model = new Category();

        $this->assertTrue($model->load($data));
        $this->assertTrue($model->save());

        $category = Category::findOne(['id' => $model->id]);

        expect($category->name)->equals($data['Category']['name']);
        expect($category->parent_id)->equals(0);
        expect($category->position)->equals($data['Category']['position']);
        expect($category->status)->notEquals($data['Category']['status']);
        expect($category->status)->equals(1);
        expect($category->publish_status)->null();
    }

    public function testCreateSuccessWithCharacteristic()
    {
        $char1 = $this->tester->grabFixture('characteristic', 1);
        $char2 = $this->tester->grabFixture('characteristic', 2);

        $data = [
            'Category' => [
                'parent_id' => null,
                'name' => 'category',
                'position' => 1,
                'status' => 'on',
                'charsName' => [
                    0 => $char1->id,
                    1 => $char2->id
                ]
            ]
        ];

        $model = new Category();
        $model->load($data);
        $model->saveCategory($data['Category']);

        /** @var $category Category*/
        $category = Category::findOne(['id' => $model->id]);

        expect($category->charsName[0]->name)->equals($char1->name);
        expect($category->charsName[1]->name)->equals($char2->name);
    }

    public function testEmpty()
    {
        $data = [
            'Category' => [
                'parent_id' => null,
                'name' => null,
                'position' => null,
                'status' => null,
                'charsName' => null
            ]
        ];

        $model = new Category();

        $this->assertTrue($model->load($data));
        $this->assertFalse($model->validate());
        $this->assertFalse($model->save());

        expect_that($model->getErrors('name'));
        expect($model->getFirstError('name'))
            ->equals('Имя категории cannot be blank.');
    }

    public function testAddSimilarCategory()
    {
        $category = $this->tester->grabFixture('category', 1);

        $data = [
            'Category' => [
                'parent_id' => null,
                'name' => $category->name,
                'position' => 1,
                'status' => 'on',
                'charsName' => null
            ]
        ];

        $model = new Category();

        $this->assertTrue($model->load($data));
        $this->assertFalse($model->validate());
        $this->assertFalse($model->save());

        expect_that($model->getErrors('name'));
        expect($model->getFirstError('name'))
            ->equals('Категория уже существует');
    }

    public function testCreateCategoryWithParent()
    {
        $category = $this->tester->grabFixture('category', 1);

        $data = [
            'Category' => [
                'parent_id' => $category->id,
                'name' => 'test',
                'position' => 1,
                'status' => 'on',
                'charsName' => null
            ]
        ];

        $model = new Category();

        $this->assertTrue($model->load($data));
        $this->assertTrue($model->validate());
        $this->assertTrue($model->save());

        $newCategory = Category::findOne(['id' => $model->id]);

        expect($newCategory->parent_id)->equals($category->id);
        expect($newCategory->name)->equals($data['Category']['name']);
        expect($newCategory->position)->equals($data['Category']['position']);
    }

    public function testEdit()
    {
        $category = $this->tester->grabFixture('category', 1);

        $data = [
            'Category' => [
                'parent_id' => null,
                'name' => 'updateCreate',
                'position' => 4,
                'status' => 'on',
                'charsName' => null
            ]
        ];

        $oldCategory = clone $category;

        $this->assertTrue($category->load($data));
        $this->assertTrue($category->save());

        expect($category->name)->equals($data['Category']['name']);
        expect($category->name)->notEquals($oldCategory->name);

        expect($category->position)->equals($data['Category']['position']);
        expect($category->position)->notEquals($oldCategory->position);
    }

    public function testEditWithCharacteristic()
    {
        $category = $this->tester->grabFixture('category', 1);
        $char = $this->tester->grabFixture('characteristic', 3);

        $data = [
            'Category' => [
                'parent_id' => null,
                'name' => 'updateCreate',
                'position' => 4,
                'status' => 'on',
                'charsName' => [
                    0 => $char->id
                ]
            ]
        ];

        $oldCategory = clone $category;

        $category->load($data);
        $category->saveCategory($data['Category']);

        $updateCategory = Category::findOne(['id' => $category->id]);

        expect(count($updateCategory->charsName))->equals(count($data['Category']['charsName']));
        expect($updateCategory->charsName[0]->name)->equals($char->name);
    }
}