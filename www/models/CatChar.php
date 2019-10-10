<?php

namespace app\models;

/**
 * This is the model class for table "cat_char".
 *
 * @property integer $id
 * @property integer $cat_id
 * @property integer $char_id
 *
 * @property Category $cat
 * @property Characteristic $char
 */
class CatChar extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cat_char';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_id', 'char_id'], 'required'],
            [['cat_id', 'char_id'], 'integer'],
            [['cat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['cat_id' => 'id']],
            [['char_id'], 'exist', 'skipOnError' => true, 'targetClass' => Characteristic::className(), 'targetAttribute' => ['char_id' => 'id']],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cat_id' => 'Cat ID',
            'char_id' => 'Char ID',
        ];
    }
    /**
     * Сохранения связи характеристики с категорией 
     * @param array $chars массив индефикаторов характеристик 
     * @param integer $id индефикатор категорий
    */
    public static function SaveСonnection($chars,$id)
    {
        CatChar::deleteAll(['cat_id' => $id]);
        foreach ($chars as $char){
            $q = new CatChar();
            $q->char_id = $char;
            $q->cat_id = $id;
            $q->save();
        }
    }

    /**
     * связь с таблицей Category
     * @return \yii\db\ActiveQuery
    */
    public function getCat()
    {
        return $this->hasOne(Category::className(), ['id' => 'cat_id']);
    }
    /**
     * связь с таблицей Category
     * @return \yii\db\ActiveQuery
    */
    public function getChar()
    {
        return $this->hasOne(Characteristic::className(), ['id' => 'char_id']);
    }
}
