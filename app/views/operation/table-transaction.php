<?php


/* @var $operations */
/* @var $pages */

use yii\grid\GridView;
use yii\helpers\Html;
?>
<div class="row"><hr>
    <div class="col-sm-6">
        <h3>Транзакция № <span class="number-transaction"><?= $transaction->transaction ?></span> (<?= $transaction->getTypeName() ?>)</h3>
        <button class="btn-del-info btn btn-success">Убрать информацию</button>
        <?php
        if($transaction->type != 3 && $transaction::className() != 'app\models\Archive'){
            echo Html::a('<i class="fa fa-print" style="font-size: 25px"></i> ', ['/'.getUrlPrintPdf($transaction)], [
                'class'=>'btn-print btn btn-print-all',
                'target'=>'_blank',
                'data-toggle'=>'tooltip',
                'title'=>'Печатает pdf'
            ]);
        }
        ?>
    </div>
    <div class="col-sm-6 text-right"></div>
    <hr>
    <div id="coming-table-container" class="col-sm-12 hide-red hide-blue hide-green hide-yellow">

        <?php $flag = true;
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => [
                'id' => 'coming-goods',
                'class' => 'info-trans-table custom-table v3',
            ],
            'showFooter' => true,
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'footer' => '',
                ],
                [
                    'header' => 'Артикул',
                    'value' => function($model){
			return $model->product->vendor_code;
                        // return $model->vproduct_id === NULL ? $model->product->vendor_code : $model->vproduct->vendor_code;
                    },
                    'footer' => '',
                ],
                [
                    'header' => 'Категория',
                    'value' => function($model){
                        return $model->product->category->name;
                    },
                    'footer' => '',
                ],
                [
                    'header' => 'Продукт',
                    'value' => function($model){
                        return $model->product->name;
                    },
                    'footer' => '',
                ],
                [
                    'header' => 'Хар-ки',
                    'value' => function($model){
			return '';
                        //return $model->vproduct_id === NULL ? 'Нет Характеристик' : $model->vproduct->chars;
                    },
                    'footer' => '',
                ],
                [
                    'header' => 'Ед. изм',
                    'value' => function($model){
                        return $model->product->unit;
                    },
                    'footer' => '',
                ],
                [
                    'header' => 'Кол-во',
                    'value' => function($model){
                        return $model->amount;
                    },
                    'footer' => '',
                ],
                [
                    'header' => 'Себ-сть $',
                    'headerOptions' => ['class' => 'red'],
                    'value' => function($model){
                        return formatedPriceUSD($model->cost_price) . ' $';
                    },
                    'contentOptions' => ['class' => 'red'],
                    'footer' => getPrintPdfButton($transaction, 'cost_price'),
                    'footerOptions' => [
                        'class' => 'red has-print',
                    ],
                ],
                [
                    'header' => 'Себ-сть ₴',
                    'headerOptions' => ['class' => 'red'],
                    'value' => function($model){
                        return formatedPriceUA(getConvertUSDinUAH($model->cost_price,getUsd())) . ' ₴';
                    },
                    'contentOptions' => ['class' => 'red'],
                    'footer' => '',
                    'footerOptions' => [
                        'class' => 'red',
                    ],

                ],
                [
                    'header' => 'Сумма $',
                    'headerOptions' => ['class' => 'red'],
                    'value' => function($model){
                        return formatedPriceUSD($model->cost_price * $model->amount) . ' $';
                    },
                    'contentOptions' => ['class' => 'red'],
                    'footer' => formatedPriceUSD($transaction->cost_price) . ' $',
                    'footerOptions' => [
                        'class' => 'red',
                    ],
                ],
                [
                    'header' => 'Сумма ₴<i class="fa fa-plus show-more-info" data-class="red" data-text="minus">',
                    'headerOptions' => ['class' => 'nred'],
                    'value' => function($model){
                        return formatedPriceUA(getConvertUSDinUAH($model->cost_price * $model->amount, getUsd())) . ' ₴';
                    },
                    'contentOptions' => ['class' => 'nred'],
                    'footer' => formatedPriceUA(getConvertUSDinUAH($transaction->cost_price,getUsd())) . ' ₴',
                    'footerOptions' => [
                        'class' => 'nred',
                    ],
                ],
                ($print_price['start_price'] && $transaction->type != 3) ? [
                    'header' => 'Цена прихода $',
                    'headerOptions' => ['class' => 'yellow'],
                    'value' => function($model){
                        return formatedPriceUSD($model->start_price) . ' $';
                    },
                    'contentOptions' => ['class' => 'yellow'],
                    'footer' => getPrintPdfButton($transaction, 'start_price'),
                    'footerOptions' => [
                        'class' => 'yellow has-print',
                    ],
                ] : ['visible' => false],
                ($print_price['start_price'])  ? [
                    'header' => 'Цена прихода ₴',
                    'headerOptions' => ['class' => 'yellow'],
                    'value' => function($model){
                        return getConvertUSDinUAH($model->start_price,getUsd()) . ' ₴';
                    },
                    'contentOptions' => ['class' => 'yellow'],
                    'footer' => '',
                    'footerOptions' => [
                        'class' => 'yellow',
                    ],
                ] : ['visible' => false],
                ($print_price['start_price'])  ? [
                    'header' => 'Сумма $',
                    'headerOptions' => ['class' => 'yellow'],
                    'value' => function($model){
                        return formatedPriceUSD($model->start_price * $model->amount) . ' $';
                    },
                    'contentOptions' => ['class' => 'yellow'],
                    'footer' => formatedPriceUSD($transaction->start_price) . ' $',
                    'footerOptions' => [
                        'class' => 'yellow',
                    ],
                ] : ['visible' => false],
                ($print_price['start_price'])  ? [
                    'header' => 'Сумма ₴<i class="fa fa-plus show-more-info" data-class="yellow" data-text="minus">',
                    'headerOptions' => ['class' => 'nyellow'],
                    'value' => function($model){
                        return getConvertUSDinUAH($model->start_price * $model->amount, getUsd()) . ' ₴';
                    },
                    'contentOptions' => ['class' => 'nyellow'],
                    'footer' => getConvertUSDinUAH($transaction->start_price,getUsd()) . ' ₴',
                    'footerOptions' => [
                        'class' => 'nyellow',
                    ],
                ] : ['visible' => false],
                ($print_price['trade_price'] && $transaction->type != 3)  ? [
                    'header' => 'Цена опт $',
                    'headerOptions' => ['class' => 'blue'],
                    'value' => function($model){
                        return formatedPriceUSD($model->trade_price) . ' $';
                    },
                    'contentOptions' => ['class' => 'blue'],
                    'footer' => getPrintPdfButton($transaction, 'trade_price'),
                    'footerOptions' => [
                        'class' => 'blue has-print',
                    ],
                ] : ['visible' => false],
                ($print_price['trade_price']) ? [
                    'header' => 'Цена опт ₴',
                    'headerOptions' => ['class' => 'blue'],
                    'value' => function($model){
                        return formatedPriceUA(getConvertUSDinUAH($model->trade_price,getUsd())) . ' ₴';
                    },
                    'contentOptions' => ['class' => 'blue'],
                    'footer' => '',
                    'footerOptions' => [
                        'class' => 'blue',
                    ],
                ] : ['visible' => false],
                ($print_price['trade_price']) ? [
                    'header' => 'Сумма $',
                    'headerOptions' => ['class' => 'blue'],
                    'value' => function($model){
                        return formatedPriceUSD($model->trade_price * $model->amount) . ' $';
                    },
                    'contentOptions' => ['class' => 'blue'],
                    'footer' => formatedPriceUSD($transaction->trade_price) . ' $',
                    'footerOptions' => [
                        'class' => 'blue',
                    ],
                ] : ['visible' => false],
                ($print_price['trade_price']) ? [
                    'header' => 'Сумма ₴<i class="fa fa-plus show-more-info" data-class="blue" data-text="minus">',
                    'headerOptions' => ['class' => 'nblue'],
                    'value' => function($model){
                        return formatedPriceUA(getConvertUSDinUAH($model->trade_price * $model->amount, getUsd())) . ' ₴';
                    },
                    'contentOptions' => ['class' => 'nblue'],
                    'footer' =>  formatedPriceUA(getConvertUSDinUAH($transaction->trade_price,getUsd())) . ' ₴',
                    'footerOptions' => [
                        'class' => 'nblue',
                    ],
                ] : ['visible' => false],

                ($print_price['price'] && $transaction->type != 3) ? [
                    'header' => 'Цена продажи $',
                    'headerOptions' => ['class' => 'green'],
                    'value' => function($model){
                        return formatedPriceUSD(getConvertUAHinUSD($model->price, getUsd())) . ' $';
                    },
                    'contentOptions' => ['class' => 'green'],
                    'footer' => getPrintPdfButton($transaction, 'price'),
                    'footerOptions' => [
                        'class' => 'green has-print',
                    ],
                ] : ['visible' => false],
                ($print_price['price']) ? [
                    'header' => 'Цена продажи ₴',
                    'headerOptions' => ['class' => 'green'],
                    'value' => function($model){
                        return formatedPriceUA(formatedPriceUA($model->price)) . ' ₴';
                    },
                    'contentOptions' => ['class' => 'green'],
                    'footer' => '',
                    'footerOptions' => [
                        'class' => 'green',
                    ],
                ] : ['visible' => false],
                ($print_price['price']) ? [
                    'header' => 'Сумма $',
                    'headerOptions' => ['class' => 'green'],
                    'value' => function($model){
                        return formatedPriceUSD(getConvertUAHinUSD($model->price * $model->amount, getUsd())) . ' $';
                    },
                    'contentOptions' => ['class' => 'green'],
                    'footer' => formatedPriceUSD($transaction->total_usd) . ' $',
                    'footerOptions' => [
                        'class' => 'green',
                    ],
                ] : ['visible' => false],
                ($print_price['price']) ? [
                    'header' => 'Сумма ₴<i class="fa fa-plus show-more-info" data-class="green" data-text="minus">',
                    'headerOptions' => ['class' => 'ngreen'],
                    'value' => function($model){
                        return formatedPriceUA($model->price * $model->amount) . ' ₴';
                    },
                    'contentOptions' => ['class' => 'ngreen'],
                    'footer' => formatedPriceUA($transaction->total_ua) . ' ₴',
                    'footerOptions' => [
                        'class' => 'ngreen',
                    ],
                ] : ['visible' => false],
            ],
        ]);
        ?>
    </div>
</div>
