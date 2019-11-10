<?php

namespace app\modules\order;

use yii\web\AssetBundle;

class OrderAsset extends AssetBundle {

    public $sourcePath = '@order-assets';
    public $css = [
        'css/order.css'
    ];
    public $js = [
        'js/order.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}
