<?php
/* @var $this yii\web\View */

use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;

$this->registerJsFile('http://code.highcharts.com/highcharts.js', [
    'depends' => 'miloschuman\highcharts\HighchartsAsset'
]);

$this->title = 'Диаграммы';
$this->params['breadcrumbs'][] = $this->title;
?>

<section class="content chart-report">
    <?= \yii\helpers\Html::dropDownList('filter_selection',[],[
        '2' => 'Категория',
        '1' => 'Поставщики'
    ],['class' => 'form-control','autofocus' => true,'style' => 'width:200px'])?>
    <div class="row">
        <div id="container" class="col-xs-6" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
        <div id="summary_table" class="col-xs-6"></div>
    </div>
    <div class="table_product" style="display: none"></div>
</section>