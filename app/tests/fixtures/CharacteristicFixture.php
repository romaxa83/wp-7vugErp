<?php
namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class CharacteristicFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Characteristic';
    public $dataFile = 'tests/_data/characteristic.php';
    public $depends = [
        'app\tests\fixtures\CharacteristicValueFixture'
    ];
}