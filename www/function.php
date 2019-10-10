<?php
/**
 * метод для отладки
*/
function debug($arr)
{
    echo '<pre>' . print_r($arr,true) .'</pre>';
}
/**
 * метод для отладки
 */
function dump($arr)
{
    echo '<pre>' . var_dump($arr) . '</pre>';
}

function dd($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    die();
}

function getUnserializeChars ($json)
{
    $chars = unserialize($json);
    if(!empty($chars)){
        $arr_v =[];
        foreach ($chars as $val){
            $r =  \app\models\CharacteristicValue::find()->select('name')->where(['id' => $val])->asArray()->one();
            $arr_v[] = $r;
        }
        $arr =  \yii\helpers\ArrayHelper::map($arr_v,'name','name');
        $val_str = implode(',',$arr);
        return $val_str;
    } else {
        return null;
    }
}
/**
 * Метод возвращает кол-во позиций выводимых на одной страницы,
 * кол-во получаеться из базы данных,установленое пользователем.
 * Принимает один параметр - название страницы.
 * Возможные значения:
 * cat - для категорий,
 * prod - для товаров,
 * operation - для транзакций,
 * store - для магазинов и контрагентов,
 * user - для пользователей,
 * price_list - для прайс-лсита.
 */
function getSizePage($name)
{
    return $_SESSION['getSettingSession'][$name];
}
/**
 * Метод возвращает курс доллара,установленого пользователем
 * в базе данных Setting
*/
function getUsd()
{
    return Yii::$app->session->get('getSettingSession')['usd'];
}

function getRequisites($name)
{
    $requisites = \app\models\Settings::find()->select(''. $name .'')->asArray()->one();
    return $requisites[$name];
}

function messageChangePrice()
{
    $mes = \app\models\Settings::find()->select('mes_change_price')->asArray()->one();
    return $mes['mes_change_price'];
}
/**
 * Метод возвращает кол-во знаков после запятой в ценах,
 * заранее установленых в базе данных Setting.
 * Принимает один параметр - 'ua' или 'usd',
 * для определеной ценны
*/
function getFloat($name)
{
    $type = ($name == 'ua') ? 'float_ua' : 'float_usd';
    return (int)Yii::$app->session->get('getSettingSession')[$type];
}

function getIdForVendor($id)
{
    if (strlen($id) == 1){
        return '00' . $id;
    } elseif (strlen($id) == 2){
        return '0' . $id;
    } else {
        return $id;
    }
}
/**
 * Метод возвращает процент из базы данных Setting
 * заранее установленое пользоватлем
 */
function getPerTradePrice()
{
    $per = \app\models\Settings::find()->select('per_trade_price')->asArray()->one();
    return $per['per_trade_price'];
}
/**
 * Метод высчитывает себестоимость.
 * Принимает 4 параметра -
 * старую цену,старое кол-во товара,
 * новую цену,новое кол-во товара.
 * Возвращает новую цену себестоимости товара
*/
function getNewCostPrice($old_price,$old_amount,$new_price,$new_amount)
{
    $old_am = $old_amount >= 0 ? $old_amount : 0;
    return (($old_price * $old_am) + ($new_price * $new_amount))/($old_am + $new_amount);
}
/**
 * Метод высчитывает старую цену.
 * Принимает 4 параметра -
 * себестоимость,старое кол-во товара,
 * новую цену,новое кол-во товара.
 * Возвращает старую приходную цену товара
 */
function getOldPrice($cost_price,$old_amount,$new_price,$new_amount)
{
    return abs(($cost_price * ($old_amount + $new_amount) - ($new_price * $new_amount))/$old_amount);
}
/**
 * Метод высчитывает оптовую цену.
 * Принимает один параметр - цену
 * Возвращает цену увеличеную на n-%
 * n - получаеться из базы данных с помощью функции getPerTradePrice
 */
function getTradePrice($price)
{
    return $price * (1 + getPerTradePrice()/100);
}

function getConvertUSDinUAH($price, $course)
{
    return $price * $course;
}

function getConvertUAHinUSD($price, $course)
{
    return $price / $course;
}

function formatedPriceUA($price)
{
    return number_format(((float)$price), getFloat('ua'), '.', '');
}

function formatedPriceUSD($price)
{
    return number_format(((float)$price), getFloat('usd'), '.', '');
}

function FormattedMessenge($description,$type = 'danger')
{
    return [
        'title' => ($type === 'danger') ? 'Ошибка' : 'Успех',
        'description' => $description,
        'type' => $type
    ];
}

function ShowMessenge($description,$type = 'danger')
{
    $_SESSION['warning'] = json_encode(FormattedMessenge($description,$type));
}

function getUrlPrintPdf($transaction,$typePrice = false)
{
    $type = '';
    $controller = '/operation-coming';
    if($transaction->type === 2){
        $controller = '/operation-consumption';
    }
    if($typePrice){ 
        $type = "&type=". $typePrice; 
    }
    return $controller ."/print-pdf?id=". $transaction->id . $type;
}

function getPrintPdfButton($transaction,$typePrice = false)
{
    if($transaction->type == 3 || $transaction::className() == 'app\models\Archive'){
        return '';
    }else{
        return "<a class='btn-print btn' href=". getUrlPrintPdf($transaction,$typePrice) ." title='Печатает pdf' target='_blank' data-toggle='tooltip'>Распечатать <i class='fa fa-print'></i> </a>";
    }
}