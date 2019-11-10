<?php
namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class OperationFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Operations';
    public $dataFile = 'tests/_data/operation.php';
}