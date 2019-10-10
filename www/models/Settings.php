<?php

namespace app\models;

/**
 * This is the model class for table "settings".
 *
 * @property integer $id
 * @property integer $usd
 * @property integer $per_trade_price
 * @property integer $cat
 * @property integer $prod
 * @property integer $operation
 * @property integer $store
 * @property integer $user
 * @property string $boss
 * @property string $name_firm
 * @property string $address
 * @property string $property
 * @property integer $mes_change_price
 * @property integer $float_ua
 * @property integer $float_usd
 */
class Settings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settings';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usd','per_trade_price','cat', 'prod', 'operation', 'store','user'], 'required'],
            [['cat', 'prod', 'operation', 'store','user','mes_change_price','float_ua','float_usd'], 'integer'],
            [['boss','name_firm','address','property'],'string'],
            [['usd','per_trade_price'],'double'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usd' => 'Usd',
            'per_trade_price' => 'Per_trade_price',
            'cat' => 'Cat',
            'prod' => 'Prod',
            'operation' => 'Operation',
            'mes_change_price' => 'Кол-во сообщений об изменении цены',
            'store' => 'Store',
            'user' => 'User',
            'boss' => 'Boss',
            'name_firm' => 'Name_firm',
            'address' => 'Address',
            'property' => 'Property',
        ];
    }

    public static function getUsd()
    {
        return Settings::find()->select(['usd'])->one()->usd;
    }
}
