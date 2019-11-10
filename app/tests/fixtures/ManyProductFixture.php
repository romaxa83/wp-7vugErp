<?php
namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class ManyProductFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Product';
    public $dataFile = 'tests/_data/many-product.php';
}