<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css',
        'css/site.css',
        'https://fonts.googleapis.com/css?family=Open+Sans:400,400i,600,600i',
        'https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap.min.css',
        'css/select2.min.css'
    ];
    public $js = [
        'js/app.min.js',
        'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js',
        'https://code.highcharts.com/highcharts.src.js',
        'https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js',
        'https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap.min.js',
        'js/table.js',
        'js/code.js',
        'js/module/SubElementSelect2.js',
        'js/select2.full.min.js',
        'js/module/LocalStoreg.js',
        'js/module/access-controle.js',
        'js/module/product.js',
        'js/module/variant-product.js',
        'js/module/agent.js',
        'js/module/category.js',
        'js/module/characteristic.js',
        'js/module/operation.js',
        'js/module/operation-consumption.js',
        'js/module/operation-coming.js',
        'js/module/operation-adjustment.js',
        'js/module/operation-mass-consumption.js',
        'js/module/operation-archive.js',
        'js/module/chart.js',
        'js/module/transferData.js',
        'js/module/archive-log.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
