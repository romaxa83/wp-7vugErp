<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class RequestFixture extends ActiveFixture
{
    public $modelClass = 'app\modules\manager\models\Request';
    public $dataFile = 'tests/_data/request.php';
}