<?php

namespace app\models;

use app\service\BazaApi;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use app\models\Characteristic;
use app\models\ProductAgent;
use app\models\Operations;
use yii\helpers\Json;
use \Datetime;
//use app\modules\logger\ActiveRecordBehavior;
/**
 * This is the model class for table "product".
 *
 * @property integer $id
 * @property string $name
 * @property integer $category_id
 * @property integer $id_char
 * @property integer $start_price
 * @property integer $cost_price
 * @property integer $trade_price
 * @property integer $price1
 * @property integer $price2
 * @property integer $amount
 * @property string $status
 * @property integer $change_price
 * @property integer $min_amount
 * @property integer $margin
 * @property integer $view_product
 *
 * @property Characteristic[] $characteristics
 * @property Category $idCategory
 */
class Product extends ActiveRecord 
{
    const STATUS_SHOP_UNACTIVE = 0;
    const STATUS_SHOP_ACTIVE = 1;
    const STATUS_SHOP_DRAFT = 2;

//    public function transactions() {
//        return [
//            ActiveRecord::SCENARIO_DEFAULT => ActiveRecord::OP_ALL,
//        ];
//    }
//    public function behaviors() {
//        return [
//            [
//                'class' => ActiveRecordBehavior::class,
//                // Список полей за изменением которых будет производиться слежение
//                // можно использовать свои методы и связи с другой моделью
//                // @see https://github.com/lav45/yii2-activity-logger
//                'attributes' => [
//                    'id',
//                    'vendor_code',
//                    'name',
//                    'category_id',
//                    'id_char',
//                    'agent_id',
//                    'amount',
//                    'unit',
//                    'start_price',
//                    'cost_price',
//                    'trade_price',
//                    'price1',
//                    'price2',
//                    'is_variant',
//                    'status',
//                    'change_price',
//                    'created_at',
//                    'updated_at',
//                    'min_amount',
//                    'margin',
//                    'view_product'
//                ]
//            ],
//            TimestampBehavior::className()
//        ];
//    }
    /**
     * Если необхадимо форматировать отобоажемое значение
     * Можно указать любой поддерживаемый формат компонентом `\yii\i18n\Formatter`
     * или использовать произвольную функцию
     * @return array
     */
    public function attributeFormats() 
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime'
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName() 
    {
        return 'product';
    }
    /**
     * @inheritdoc
     */
    public function rules() 
    {
        return [
            [['min_amount','margin'], 'required', 'message' => 'поле {attribute} не заполнено'],
            ['name', 'required', 'message' => 'Наименование товара не заполнено'],
            ['price1', 'required', 'message' => 'Цена 1 не была заполненная'],
            ['price2', 'required', 'message' => 'Цена 2 не была заполненная'],
            ['category_id', 'required', 'message' => 'Категория товара не заполненная'],
            ['agent_id', 'required', 'message' => 'Поставщик товара не заполнен'],
            [['category_id', 'amount', 'is_variant', 'vendor_code', 'min_amount', 'margin','view_manager','status','publish_status'], 'integer'],
            [['start_price', 'cost_price', 'trade_price', 'price1', 'price2'], 'double'],
            [['amount', 'start_price', 'cost_price', 'trade_price', 'price1', 'price2', 'status'], 'default', 'value' => 0],
            [['is_variant'], 'default', 'value' => 1],
            [['unit'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'filter', 'filter' => 'trim'],
            [['name'], 'string', 'min' => 2, 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['margin'], 'default', 'value' => getPerTradePrice()],
            ['name', 'unique','when' => function(){
                if($this->isNewRecord){
                    return true;
                }elseif($this->name != $this->oldAttributes['name']){
                    return true;
                }
            }],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels() 
    {
        return [
            'id' => 'ID',
            'vendor_code' => 'Артикул',
            'name' => 'Наименование товара',
            'category_id' => 'Категория товара',
            'id_char' => 'Характеристики товара',
            'agent_id' => 'Поставщик',
            'amount' => 'Кол-во',
            'unit' => 'Ед.из.',
            'start_price' => 'Цена($)',
            'cost_price' => 'Себест. цена($)',
            'trade_price' => 'Опт. цена($)',
            'price1' => 'Цена1 (ua)',
            'price2' => 'Цена2 (ua)',
            'is_variant' => 'Вариации товара',
            'status' => 'Статус',
            'change_price' => 'Изменение цены',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата последнего изменения',
            'min_amount' => 'Мин. кол-во товара',
            'margin' => 'Маржа по цене',
            'view_manager' => 'Просмотр для менеджера'
        ];
    }
    /**
     * Метод сохраняет продукту нового агента, если это не основной агент
     * и такого агента нет среди дополнительных
     * @param integer $agent_id индефикатор агента 
    */
    public function setNewAgents($agent_id)
    {
        $model = ProductAgent::find()->where(['product_id'=>$this->id])->andWhere(['agent_id'=>$agent_id])->all();
        if(empty($model) && $this->agent_id != $agent_id){
            $model = new ProductAgent();
            $model->product_id = $this->id;
            $model->agent_id = $agent_id;
            $model->save();
        }
    }
    /**
     * функция возвращает json-обьект для строительства диаграммы
     * @param array $arr массив ,где ключ-id,а значение название
     * @param integer $type тип,(либо "category",либо"agent")
     * @return json
    */
    public static function BuildChart($arr, $type) 
    {
        $product_query = Product::find();
        //получаем общее кол-во товаров
        $count = $product_query->count();
        $all_product = $product_query->asArray()->all();
        $arra = [];
        foreach ($arr as $id => $one) {
            $test = [];
            foreach ($all_product as $product) {

                if ($product[$type.'_id'] == $id) {
                    $test[] = $product;
                }
            }
            $arra[$id] = $test;
        }
        $total = [];
        foreach ($arra as $i => $v) {
            if (count($v) != 0) {
                $w = count($v);
                $total[$i] = $w;
            }
        }
        $arr_chart = [];
        $in = 0;
        foreach ($total as $i => $n) {
            $v = $in++;
            if ($type == 'category') {
                $name = self::getCategoryForName($i);
                $arr_chart[$v]['name'] = $name['name'];
                $arr_chart[$v]['x'] = $n;
                $perc = ($n * 100) / $count;
                $arr_chart[$v]['y'] = $perc;
            }
            if ($type == 'agent') {
                $name = self::getAgentForFirm($i);
                $arr_chart[$v]['name'] = $name['firm'];
                $arr_chart[$v]['x'] = $n;
                $perc = ($n * 100) / $count;
                $arr_chart[$v]['y'] = $perc;
            }
        }
        $send_arr = [];
        $send_arr[0] = $arr_chart;
        $send_arr[1] = $type;
        return JSON::encode($send_arr);
    }
    /**
     * Переопределение beforeSave для сохранения времени создания при новой записи и времени изменения
     * @return boolean
    */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert))
        {
            $date = new DateTime();

            if ($this->isNewRecord){
                $this->created_at = $date->getTimestamp();
            } else {
                if($this->isAttributeChanged('price1') || $this->isAttributeChanged('price2')){
                    $this->change_price = messageChangePrice();
                }
            }

            $this->updated_at = $date->getTimestamp();

            return true;
        } else {
            return false;
        }
    }
    /**
     * Переопределение afterSave для сохранения вендор кода при новой записи
    */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if($insert) {            
            $this->setVendorCode();
        }
    }
    /**
     * Установка вендор кода товару
    */
    public function setVendorCode()
    {
        $vendor_code = getIdForVendor($this->category_id);
        $vendor_code .= getIdForVendor($this->agent_id);
        $this->vendor_code = getIdForVendor($this->id) . $vendor_code;
        $this->update();
        if($this->is_variant == 2){
            $this->updateVendorCodeVProducts();
        }
    }
    /**
     * Обновление вендор кода вариативным товарам
     */
    public function updateVendorCodeVProducts()
    {
        $v_products = $this->getVproducts()->all();
        if(!empty($v_products)) {
            foreach ($v_products as $v_product){
                $v_product->vendor_code = $this->vendor_code . '-' . $v_product->id;
                $v_product->update();
            }
        }
    }

    public function getLastStartPrice()
    {
        if(isset($this->id) && is_null($this->id)){
            throw new \yii\web\HttpException(404, 'The requested Item could not be found.');
        }
        
        $operation = Operations::find()
            ->select('operations.id , oper_coming.amount as coming_amount, oper_coming.transaction_id as coming_transaction_id, oper_coming.start_price as coming_price , oper_adjustment.transaction_id as adjustment_transaction_id , oper_adjustment.start_price as adjustment_price')
            ->leftJoin('`oper_coming`', '`oper_coming`.`transaction_id` = `operations`.`id`')
            ->leftJoin('`oper_adjustment`', '`oper_adjustment`.`transaction_id` = operations.`id`')
            ->where(['`oper_coming`.`product_id`' => $this->id])
            ->orWhere(['`oper_adjustment`.`product_id`' => $this->id])
            ->andWhere(['>','`oper_coming`.`amount`',0])
            ->orderBy(['`operations`.`id`' => SORT_DESC])
            ->asArray()
            ->limit(2)  
        ->all(); 

        $operation = array_reverse($operation);
        if(!empty($operation)){
            if(isset($operation[1])){
                return is_null($operation[1]['adjustment_price']) ? $operation[1]['coming_price'] : $operation[1]['adjustment_price'];
            }else{
                return is_null($operation[0]['adjustment_price']) ? $operation[0]['coming_price'] : $operation[0]['adjustment_price'];
            }
        }else{
            return $this->start_price;
        }
    }
    /**
     * Связь один ко многим с моделью Category
     * @return \yii\db\ActiveQuery
    */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
    /**
     * Связь один ко многим с моделью Agent
     * @return \yii\db\ActiveQuery
     */
    public function getAgent()
    {
        return $this->hasOne(Agent::className(), ['id' => 'agent_id']);
    }
    
    public function getProductAgent() {
        return $this->hasMany(ProductAgent::className(), ['product_id' => 'id']);
    }
    /**
     * Связь один ко многим с моделью VProduct
     * @return \yii\db\ActiveQuery
    */
    public function getVproducts()
    {
        return $this->hasMany(VProduct::className(),['product_id' => 'id']);
    }
    
    public function getAdditionalAgents(){
        return $this->hasMany(ProductAgent::className(), ['product_id' => 'id'])->asArray();
    }

    public static function getCategoryForName($id) 
    {
        return Category::find()->select('name')->where(['id' => $id])->asArray()->one();
    }

    public static function getAgentForFirm($id) 
    {
        return Agent::find()->select('firm')->where(['id' => $id])->asArray()->one();
    }
}
