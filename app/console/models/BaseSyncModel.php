<?php
namespace app\console\models;

use app\models\Settings;
use Yii;

class BaseSyncModel 
{
    private $arrayTypeData = [
        'double',
        'int',
        'boolean'
    ];

    public function changeCourse($one)
    {
        $model = Settings::find()->one();
        $model->usd = $one['requestData']['body'];
        
        if(!$model->save()){
            Curl::sendMsgTelegram('telegram', [
                'message' => "course not save",
                'type' => 'alert'
            ]);

            return false;

        }
    }

    public function truncate_number( $number, $precision = 2) 
    {
        // Zero causes issues, and no need to truncate
        if ( 0 == (int)$number ) {
            return $number;
        }
        // Are we negative?
        $negative = $number / abs($number);
        // Cast the number to a positive to solve rounding
        $number = abs($number);
        // Calculate precision number for dividing / multiplying
        $precision = pow(10, $precision);
        // Run the math, re-applying the negative value to ensure returns correctly negative / positive
        return floor( $number * $precision ) / $precision * $negative;
    }

    public function getTypeError($new,$old,$typeData = null)
    {
        $result = 'alert';

        if(in_array($typeData,$this->arrayTypeData)){
            $typingNew = is_null($typeData) ? $new : $this->typingData($typeData,$new);
            $typingOld = is_null($typeData) ? $old : $this->typingData($typeData,$old);

            if(is_null($typingNew) || is_null($typingNew)){
                return 'empty data for typing';
            }

            $result = ($typingNew > $typingOld) ? $typingNew - $typingOld : $typingOld - $typingNew;

            $result = (int)$result;

            $rate = Yii::$app->params;

            if(!isset($rate['errorRate'])){
                $result = 'You must set in params.php {errorRate}';
            }else{
                $result = ($result > $rate['errorRate']) ? 'alert' : 'warning';
            }
        }

        return $result;
    }

    private function typingData($type,$value)
    {
        switch(true){
            case $type === 'double' : 
                $result = (double)$value;
            break;

            case $type === 'int' : 
                $result = (int)$value;
            break;

            case $type === 'boolean' : 
                $result = (boolean)$value;
            break;

            default : break;
        }

        return $result ?? null;
    }
}