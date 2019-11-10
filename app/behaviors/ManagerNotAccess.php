<?php

namespace app\behaviors;

use yii\base\Behavior;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class ManagerNotAccess extends Behavior
{
    public $actions = null;
    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'access'
        ];
    }
    public function access(){
        foreach($this->actions as $action){
            if($this->owner->action->id == $action){
                if (\Yii::$app->user->can('manager')) {
                    throw new ForbiddenHttpException('Доступ закрыт.');
                }
            }
        }
    }
}