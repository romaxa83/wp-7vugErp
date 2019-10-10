<?php

namespace app\models;

use Yii;
/**
 * This is the model class for table "v_product".
 *
 * @property int $id
 * @property int $product_id
 * @property int $amount
 * @property double $price1
 * @property double $price2
 * @property string $char_value
 * @property string $date_create
 * @property string $date_update
 */
class VProduct extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_product';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'amount'], 'integer'],
            [['price1', 'price2'], 'number'],
            [['char_value'], 'string'],
            [['date_create', 'date_update'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'amount' => 'Amount',
            'price1' => 'Price1',
            'price2' => 'Price2',
            'char_value' => 'Char Value',
            'date_create' => 'Date Create',
            'date_update' => 'Date Update',
        ];
    }
    /**
     * функция комбинирует значения характеристики
     * @param array $arr массив ,где ключ id-характеристики,а значение id-значения-характеристики
     * @return array 
    */
    public static function СombinationOfCharacteristics($arr){
        $result = array();
        $total = count($arr);
        while(true) {
            $row = array();
            foreach ($arr as $key => $value) {
                    $row[] = current($value);
            }
            $result[] = $row;
            for ($i = $total - 1; $i >= 0; $i--) {
                if (next($arr[$i])) {
                    break;
                }
                elseif ($i == 0) {
                    break 2;
                }
                else {
                    reset($arr[$i]);
                }
            }
        }
        return $result;
    }
    /**
     * функция возвращает массив c подготовлеными группами
     * @param array $arr 
     * @return array  
    */
    public static function CreateGroupFromCombination($arr)
    {
        $result = [];
        foreach ($arr as $one){
            foreach ($one as $item){
                foreach($item as $key => $oneItem){
                    $items[$key] = $oneItem;
                }   
            }
            $result[] = $items;
            $items = [];
        }
        return $result;
    }
    /** дописать  
     * функция возвращает ровное количество для каждого экземпляра вариативного товара
     * @param integer $countProd количество товаров 
     * @param integer $baseAmount количество у базового товара
     * @return array 
    */
    public static function GetAmountForOneProd($countProd,$baseAmount)
    {
        if($countProd == $baseAmount){
            $result['oneProd'] = 1;
        }else{
            $forOne = (int)abs($baseAmount/$countProd);
            $amount =  $forOne == 0 ? 1 : $forOne;
            for($i=0;$i<$countProd;$i++){
                (!isset($result[$i])) ? $result[$i] = 0 : null;
                if($baseAmount != 0){
                    for($z=0;$z<$amount;$z++){
                        $result[$i]++;
                        $baseAmount--;
                    }
                }
            }
            $i = 0;
            while ($baseAmount != 0){
                $result[$i]++;
                $baseAmount--;
                $i++;
                if($i == count($result)) $i = 0;
            }
        }
        return $result;
    }
    /**
     * функция возвращает строку значений характеристики по принятой строке 
     * @param stirng $charValue сеарилизованая строка 
     * @return string $str строка характеристик вариаций 
    */
    public static function getCharValueFromId($charValue)
    {
        $charValue = unserialize($charValue);
        $str = '';
        foreach ($charValue as $key => $oneChar){
            $oneChar = CharacteristicValue::find()->select(['name'])->where(['id'=>$oneChar])->asArray()->one();
            $str .=' | '.$oneChar['name'];
        }
        return $str;
    }
    /**
     * функция возвращает новые группы характеристик
     * @param array $arr_chars массив групп характеристик  
     * @param integer $id индефикатор товара 
     * @return array 
    */
    public static function GetUniqueChar_value($arr_chars,$id)
    {
        $product = VProduct::find()->select(['char_value','id'])->where(['product_id'=>$id])->asArray()->all();
        foreach ($arr_chars as $oneCharGroup){
            $new = unserialize($oneCharGroup);
            $status = 'record';
            foreach ($product as $oneProduct){
                $old = unserialize($oneProduct['char_value']);
                if($new == $old){
                    $id = $oneProduct['id'];
                    $status = 'stop';
                    break;
                }
            }
            if($status != 'stop'){
                $result[] = serialize($new);
            }else{
                $result['old'][] = $id;
            }
        }
        if(isset($result)){
            return $result;
        }
    }
    /**
     * Сохранения/обновления вариаций товара
     * @param array $chars массив вариаций 
     * @param array $post данные базового и вариаций нужные для создания вариций 
    */
    public function saveVariantProduct($chars,$post,$old = '')
    {
        $i=0;
        for(;$i<count($chars);$i++){
            $var_product = new VProduct();
            $var_product->product_id = $post['product_id'];
            $var_product->amount = $post['vproduct']['amount'][$i];
            $var_product->price1 = $post['vproduct']['price1'][$i];
            $var_product->price2 = $post['vproduct']['price2'][$i];
            $var_product->char_value = $chars[$i];
            $var_product->date_create = date("Y-m-d H:i:s"); 
            $var_product->save();
            $var_product->vendor_code = $post['vendor_code'].'-'.$var_product->id; 
            $var_product->save();
            $error = $var_product->getErrorSummary(true);
        }
        if(!empty($old)){
            foreach($old as $one){
                $var_product = VProduct::findOne(['id' => $one]);
                $var_product->amount = $post['vproduct']['amount'][$i];
                $var_product->price1 = $post['vproduct']['price1'][$i];
                $var_product->price2 = $post['vproduct']['price2'][$i];
                $var_product->date_update = date("Y-m-d H:i:s"); 
                $var_product->update();
                $i++;
                $error = $var_product->getErrorSummary(true);
            }
        }
        return $error;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert))
        {
            if (!$this->isNewRecord){
                if($this->isAttributeChanged('price1') || $this->isAttributeChanged('price2')){
                    $this->change_price = messageChangePrice();
                }
            }
            return true;
        } else {
            return false;
        }
    }
    
    public function afterSave($insert,$changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (!$this->isNewRecord){
            $this->date_update = date("Y-m-d H:i:s");
        }
    }
    /**
     * Связь много к одному с моделью Product
     * @return \yii\db\ActiveQuery
    */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    public function getChars()
    {
        $charValue = unserialize($this->char_value);
        $str = '';
        foreach ($charValue as $key => $oneChar){
            $oneChar = CharacteristicValue::find()->select(['name'])->where(['id'=>$oneChar])->asArray()->one();
            $str .=' | '.$oneChar['name'];
        }
        return $str;
    }

    public static function RenderAjaxVProduct($id,$base_name,$price_type,$vproduct_exist)
    {
        $vproduct = self::find()->where(['product_id' => $id])->all();
        if ($vproduct_exist){
            $vproduct = self::find()->where(['product_id' => $id])->andWhere(['not in','id',$vproduct_exist])->all();
        }
        $variant_product = [];
        foreach ($vproduct as $k => $v){
            $variant_product[$k]['id'] = $v['id'];
            $variant_product[$k]['price'] = $price_type == 1 ? $v['price1'] : $v['price2'];
            $variant_product[$k]['chars'] = self::getCharValueFromId($v['char_value']);
        }

        if(!($char_values = Operations::getNameCharsAndValue($vproduct))){
            return 'empty';
        }
        return Yii::$app->view->renderAjax('/request/variant-table',[
            'base_product' => ['name' => $base_name,'id' => $id],
            'variant_product' => $variant_product,
            'char_values' => $char_values
        ]);
    }
}
