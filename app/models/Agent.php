<?php

namespace app\models;

use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use \Datetime;
/**
 * This is the model class for table "agent".
 *
 * @property integer $id
 * @property string $name
 * @property string $address
 * @property string $firm
 * @property string $telephone
 * @property string $data
 * @property integer $type
 * @property integer $price_type
 * @property integer $status
 * @property integer $is_main
 * @property string $created_at
 * @property string $updated_at
 */
class Agent extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'agent';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            [['firm', 'type'], 'required'],
            [['type','price_type'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'address', 'firm','telephone'], 'string', 'max' => 255],
            [['firm'], 'unique']
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'И.О.Фамилия',
            'address' => 'Адрес компании',
            'firm' => 'Название компании',
            'telephone' => 'Телефон компании',
            'data' => 'Данные(реквизиты)',
            'price_type' => 'Тип цены',
            'type' => 'Тип',
            'status' => 'Статус',
            'is_main' => 'Главный',
            'created_at' => 'Время создания контрагента',
            'updated_at' => 'Последнне редактирование',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    /**
     * Метод получения массива моделей Agent
     * @param integer $type Тип модели Agent 1 - поставщик 2 - магазин
     * @param boolean $asArray возращать модель массивом или обьектом
     * @return array  массив моделей Agent
    */
    public static function getAllAgent($type = 1,$asArray = true,$status = 1)
    {
        return self::find()->where(['type' => $type])->andWhere(['status' => $status])->asArray($asArray)->all();
    }
    /**
     * Метод получения массива моделей Agent
     * @param integer $type Тип модели Agent 1 - поставщик 2 - магазин
     * @param boolean $asArray возращать модель массивом или обьектом
     * @return array  массив моделей Agent
     */
    public static function getAgentsMap($type = 1,$asArray = true,$status = 1)
    {
        return ArrayHelper::map(self::getAllAgent($type,$asArray,$status), 'id', 'firm');
    }
    /**
     * Метод возвращает массив магазинов с последним добавленным менеджером
     * @return array Список магазинов с последним добавленым менеджером
    */
    public static function getStoresWithManager()
    {
        $store = Agent::find()->select(['id','firm','name'])->where(['type' => 2])->asArray()->all();
        $managers = User::find()->select(['username', 'store_id'])->where((['role' => 'manager']))->asArray()->all();
        $managersMap = ArrayHelper::map($managers, 'store_id', 'username');
        $stores = [];
        foreach ($store as $one){
            if (!isset($managersMap[$one['id']])){
                $one['name'] ='НЕТ МЕНЕДЖЕРА';
            } else {
                $one['name'] = $managersMap[$one['id']];
            }
            $stores[$one['id']] = $one['firm'] . ' ( ' . $one['name'] .' )';
        }
        return $stores;
    }
    /**
     * Метод сохранения модели Agent 
     * @param  array $data параметры модели Agent
     * @param boolean $ajax добавления агента выполняеться ajax или submit form
     * @return array возращаються ошибки валидаций 
    */
    public function saveAgent($data,$ajax,$model) 
    {
        $date = new DateTime();
        $model->firm = $data['firm'];
        $model->address = $data['address'];
        $model->telephone = $data['telephone'];
        $model->data = $data['data'];
        ($ajax) ? $model->type = 1 : null;
        (isset($data['status']) && $data['status'] == 'on') ? $model->status = 1 : $model->status = 0;
        ($model->isNewRecord) ? $model->created_at = $date->getTimestamp() : $model->updated_at = $date->getTimestamp();
        if($model->type == 2) {
            $model->price_type = 1;
        }else{
            $model->price_type = 0;
        }
        if(isset($data['price_type'])){
            $model->price_type = $data['price_type'];
        }
        $model->save();
        return $model;
    }
}
