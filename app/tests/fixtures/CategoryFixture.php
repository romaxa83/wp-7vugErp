<?php
namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class CategoryFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Category';
    public $dataFile = 'tests/_data/category.php';
    public $depends = [
        'app\tests\fixtures\CharacteristicValueFixture',
        'app\tests\fixtures\CharacteristicFixture',
        'app\tests\fixtures\CatCharFixture'
    ];
}