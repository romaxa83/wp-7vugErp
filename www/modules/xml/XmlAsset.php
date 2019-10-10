<?php

namespace app\modules\xml;

use yii\web\AssetBundle;

class XmlAsset extends AssetBundle {

    public $sourcePath = '@xml-assets';
    public $css = [
        'css/xml.css'
    ];
    public $js = [
        'js/xml.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}
