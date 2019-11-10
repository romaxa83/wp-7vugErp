<?php
namespace app\console\controllers;

use app\models\User;
use yii\helpers\Console;

class SeedController extends \yii\console\Controller
{
    /**
     * Заполняет поле "old_auth_key" в модели user
     * @package app\commands
     */
    public function actionAuthKey()
    {
        $users = User::find()->all();

        if(!$users){
            $this->stdout('Users not found' . PHP_EOL, Console::FG_RED);
        }

        foreach($users as $user){
            $user->old_auth_key = $user->auth_key;
            $user->save();
        }
        $this->stdout('Success' . PHP_EOL, Console::FG_GREEN);
    }
}