<?php

namespace app\modules\manager;

use yii\web\AssetBundle;

class ManagerAsset extends AssetBundle {

    public $sourcePath = '@manager-assets';
    public $css = [
        'css/manager.css',
        'css/request.css'
    ];
    public $js = [
        'js/manager.js',
        'js/admin.js',
        'js/request.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}
