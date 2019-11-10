<?php

namespace app\models;

/**
 * This is the model class for table "characteristic".
 *
 * @property integer $id
 * @property string $name
 * @property integer $id_product
 * @property integer $status
 *
 * @property CatChar[] $catChars
 * @property Product $idProduct
 * @property CharacteristicValue[] $characteristicValues
 */
class Characteristic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'characteristic';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'],'filter','filter' => 'trim'],
            [['name'], 'string','min' => 2,'max' => 255],
            [['name'], 'unique',  'message' => 'Имя уже существует']
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название характеристики'
        ];
    }

    public function getProducts(){

        return $this->hasMany(Product::className(),['id' => 'id_prod'])
            ->viaTable('prod_char',['id_char' => 'id']);
    }

    /**
     * //связь с таблицей катеорий через промежуточную таблицу
     * @return \yii\db\ActiveQuery
     */
    public function getCategories(){

        return $this->hasMany(Category::className(),['id' => 'cat_id'])
            ->viaTable('cat_char',['char_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
   public function getCharacteristicValues()
    {
        return $this->hasMany(CharacteristicValue::className(), ['id_char' => 'id']);
    }
}
