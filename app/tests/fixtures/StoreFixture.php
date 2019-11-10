<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class StoreFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Agent';
    public $dataFile = 'tests/_data/store.php';
}