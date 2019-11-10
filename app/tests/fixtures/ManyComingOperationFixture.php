<?php
namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class ManyComingOperationFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Operations';
    public $dataFile = 'tests/_data/many-coming-operation.php';
}