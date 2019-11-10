<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "characteristic_value".
 *
 * @property integer $id
 * @property string $name
 * @property integer $id_char
 *
 * @property Characteristic $idChar
 */
class CharacteristicValue extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'characteristic_value';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'id_char'], 'required'],
            [['id_char'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['id_char'], 'exist', 'skipOnError' => true, 'targetClass' => Characteristic::className(), 'targetAttribute' => ['id_char' => 'id']],
            [['name'], 'unique', 'targetAttribute' => ['name', 'id_char'],  'message' => 'Имя уже существует']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Значение характеристики',
            'id_char' => '',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdChar()
    {
        return $this->hasOne(Characteristic::className(), ['id' => 'id_char']);
    }
}
