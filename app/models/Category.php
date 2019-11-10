<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "category".
 *
 * @property integer $id
 * @property string $name
 * @property integer $parent_id
 * @property integer $position
 * @property integer $status
 * @property integer $publish_status
 *
 * @property CatChar[] $catChars
 * @property Product[] $products
 */
class Category extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            ['name', 'unique', 'message' => 'Категория уже существует'],
            [['position'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['position','parent_id'], 'default', 'value' => 0],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя категории',
            'parent_id' => 'Parent ID',
            'position' => 'Позиция',
            'status' => 'Статус',
        ];
    }
    /**
     * Вспомогательный метод для фармирирования вложености Category
     * @param array $mas Массив моделей Category
     * @param integer $parent_id Индефикатор родительской категорий
     * @return array
    */
    static function addItem($mas, $parent_id) 
    {
        $data = [];
        foreach ($mas as $k => $v) {
            if ($v['parent_id'] == $parent_id) {
                $data[$k]['parent'] = $v;
                $data[$k]['child'] = self::addItem($mas, $v['id']);
            }
        }
        return $data;
    } 
    //рекурсивное получения строки индефикаторов категорий которые лежат в иерархия высше 
    static function GetIdParent($CategoryList,$CategoryId){
        $data = '';
        foreach ($CategoryList as $k => $v) {
            if ($k == $CategoryId) {
                $data .= $v['id'] . ','; 
                if($v['parent_id'] != 0){
                    $data .= self::GetIdParent($CategoryList, $v['parent_id']);
                }
            }
        }
        return $data;
    }    
    //рекурсивное получения строки индефикаторов категорий которые лежат в иерархия ниже
    static function GetIdChild($CategoryList,$CategoryId){
        $data = '';
        foreach ($CategoryList as $k => $v){
            $data .= $v['parent']['id'] . ',';
            if(!empty($v['child'])){
                $data .= self::GetIdChild($v['child'], $CategoryId);
            }
        }
        return $data;
    }
    //возвращает массив с категориями и дочерними категориями
    public static function getListCategory($paramLevel = false)
    {
        $parentQuery = Category::find()->select(['id','name','parent_id'])->where(['status'=>1])->andWhere(['=','parent_id',0])->asArray()->all();
        $parent = ArrayHelper::map($parentQuery,'id','name');
        $childQuery = Category::find()->select(['id','name','parent_id'])->where(['status'=>1])->andWhere(['!=','parent_id',0])->asArray()->all();
        $child = ArrayHelper::map($childQuery,'id','name','parent_id');
        $category = [];
        foreach ($parent as $id => $one){
            $category[$id] = $one; 
            if(array_key_exists($id,$child)){
                $resultRecursive = Category::Recursive($paramLevel,$id,$child,$parent);
                $category = $category + $resultRecursive;
            }
        }
        return $category;
    }
    
    private static function Recursive($paramLevel,$id,$child,$parents,$i = 2,$response = [])
    {
        foreach($child[$id] as $childId => $one){
            switch (true){
                case $paramLevel : $response[$childId] = $i . '|' . $one; break;
                case ($i == 2) : $response[$childId] = '--' .$one; break;
                case ($i == 3) : $response[$childId] = '---' .$one; break;
                case ($i == 4) : $response[$childId] = '----' .$one; break;
                case ($i == 5) : $response[$childId] = '-----' .$one; break;
                default : break;
            }
            if(array_key_exists($childId,$child)){
                $i++;
                $resultRecursive = Category::Recursive($paramLevel,$childId,$child,$parents,$i,$response);
                $response = $response + $resultRecursive;
                $i--;
            }
        }
        return $response;
    }
    /**
     * Получения массива характеристик 
     * @return array cтруктура [индекс => ['id'] => name]
    */
    public function getArrCharacteristic()
    {
        return ArrayHelper::map(Characteristic::find()->all(),'id','name');
    }
    /**
     * функция для получения модели и списка характеристик
     * @return array 1 - елемент массива новый экземляр класса Category 2 - елемент массива список характеристик
    */
    public static function getVariableForForm()
    {
        $model = new Category();
        $chars_cat = $model->getArrCharacteristic();
        return [$model,$chars_cat];
    }
     /**
     * Метод сохранения модели Category 
     * @param  array $data параметры модели Category
     * @return boolean 
    */
    public function saveCategory($data)
    { 
        if(isset($data['parent_id']) && ($data['parent_id'] == $this->id && !$this->isNewRecord)){
            $this->addError('parent_id','Категория не может быть собственым родителям');
            $this->parent_id = 0;
        }else{
            $this->status = (isset($data['status'])) ? 1 : 0;
            $this->save();
            if(isset($data['charsName']) && !empty($data['charsName'])){
                CatChar::SaveСonnection($data['charsName'], $this->id);
            }
        }
        return $this->getErrorSummary(true);
    }
    
    public function getSelectedChars()
    {
        $selectedChars = $this->getChars()->select('id')->asArray()->all();
        return ArrayHelper::getColumn($selectedChars,'id');
    }
    /**
     * связь с таблицей CatChar
     * @return \yii\db\ActiveQuery
    */
    public function getCatChars()
    {
        return $this->hasMany(CatChar::className(), ['cat_id' => 'id']);
    }
    /**
     * связь с таблицей Product
     * @return \yii\db\ActiveQuery
    */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['category_id' => 'id']);
    }
    /**
     * связь с таблицей CharacteristicValue через промежуточную таблицу
     * @return \yii\db\ActiveQuery
    */
    public function getChars()
    {
        return $this->hasMany(CharacteristicValue::className(),['id_char' => 'char_id'])
            ->viaTable('cat_char',['cat_id' => 'id']);
    }
    /**
     * связь с таблицей Characteristic через промежуточную таблицу
     * @return \yii\db\ActiveQuery
    */
    public function getCharsName()
    {
        return $this->hasMany(Characteristic::className(),['id' => 'char_id'])
            ->viaTable('cat_char',['cat_id' => 'id']);
    }
}
