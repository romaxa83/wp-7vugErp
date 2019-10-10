<?php
namespace app\modules\logger\service;

use Yii;

class LogService 
{
    public static function logModel($model,string $action)
    {
        if(YII_ENV !== 'test'){
            $name = explode('\\',get_class($model));
            $collection = Yii::$app->activityLogger->createCollection(end($name));
            $collection->setAction($action);
            $collection->setEntityId($model->id);

            $collectionWithMessage = $collection->formattedMessage($model,$collection);

            $collectionWithMessage->push();
        }
    }
}
