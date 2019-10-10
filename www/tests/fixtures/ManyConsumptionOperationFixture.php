<?php
namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class ManyConsumptionOperationFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Operations';
    public $dataFile = 'tests/_data/many-consumption-operation.php';
}