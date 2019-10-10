<?php
namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class ProductFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Product';
    public $dataFile = 'tests/_data/product.php';
}