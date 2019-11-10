<?php

namespace app\models;

use Yii;
use app\modules\logger\ActiveRecordBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "access".
 */
class Access extends ActiveRecord
{

    public function transactions()
    {
        return [
            ActiveRecord::SCENARIO_DEFAULT => ActiveRecord::OP_ALL,
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => ActiveRecordBehavior::class,
                // Список полей за изменением которых будет производиться слежение
                // можно использовать свои методы и связи с другой моделью
                // @see https://github.com/lav45/yii2-activity-logger
                'attributes' => [
                    'name',
                    'controller',
                    'action',
                    'status'
                ]
            ]
        ];
    }

    /**
     * Если необхадимо форматировать отобоажемое значение
     * Можно указать любой поддерживаемый формат компонентом `\yii\i18n\Formatter`
     * или использовать произвольную функцию
     * @return array
     */
    public function attributeFormats()
    {
        return [
            'published_at' => 'datetime',
            'is_published' => function($value) {
                return Yii::$app->formatter->asBoolean($value);
            },
            'status' => function($value) {
                return ($value == 0) ? 'Отключен' : 'Включен';
            }
        ];
    }
  
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'access';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'controller', 'action', 'status'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'controller' => 'Контроллер',
            'action' => 'Действие',
            'weight' => 'Вес',
            'status' => 'Статус'
        ];
    }

}
