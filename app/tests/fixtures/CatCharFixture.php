<?php
namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class CatCharFixture extends ActiveFixture
{
    public $modelClass = 'app\models\CatChar';
    public $dataFile = 'tests/_data/cat-char.php';
}