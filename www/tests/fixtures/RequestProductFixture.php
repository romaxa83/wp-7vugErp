<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class RequestProductFixture extends ActiveFixture
{
    public $modelClass = 'app\modules\manager\models\RequestProduct';
    public $dataFile = 'tests/_data/request-product.php';
}