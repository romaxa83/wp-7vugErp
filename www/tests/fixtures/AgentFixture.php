<?php
namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class AgentFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Agent';
    public $dataFile = 'tests/_data/agent.php';
}