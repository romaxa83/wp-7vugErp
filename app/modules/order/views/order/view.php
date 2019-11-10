<?php

use app\modules\order\OrderAsset;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;

OrderAsset::register($this);
?>

<div class="order-page content">
    <div class="row">
        <div class="col-md-12">
            <?php
            // 'order_id', 'product_id', 'amount', 'price', 'confirm'
            //var_dump($dataProvider);
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => [
                    'class' => 'custom-table v3 table-fix'
                ],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'product.vendor_code',
                        'value' => function($model) {
                            return $model['product']['vendor_code'];
                        }
                    ],
                    [
                        'attribute' => 'product.category.name',
                        'value' => function($model) {
                            return $model['product']['category']['name'];
                        }
                    ],
                    [
                        'attribute' => 'product.name',
                        'value' => function($model) {
                            return $model['product']['name'];
                        }
                    ],
                    [
                        'attribute' => 'vProduct.char_value',
                        'label' => 'Вариации',
                        'value' => function($model) {
                            return $model['vProduct']['char_value'];
                        }
                    ],
                    [
                        'attribute' => 'amount',
                        'value' => function($model) {
                            return $model['amount'];
                        }
                    ],
                    [
                        'attribute' => 'confirm',
                        'format' => 'raw',
                        'value' => function($model) {
                            return '<input id="confirm-order-product-' . $model['id'] . '" class="confirm-order-product hidden" type="checkbox" ' . (($model['confirm'] == 1) ? 'checked' : FALSE) . ' data-id="1">'
                                    . '<label for="confirm-order-product-' . $model['id'] . '" data-text-true="Вкл" data-text-false="Выкл" title="Подтвердить"><i></i></label>';
                        }
                    ]
                ]
//                'columns' => [
//                    ['class' => 'yii\grid\SerialColumn'],
//                    [
//                        'attribute' => 'order_id',
//                        'value' => function($model) {
//                            return $model->order;
//                        }
//                    ],
//                    [
//                        'attribute' => 'date',
//                        'value' => function($model) {
//                            return $model->date;
//                        }
//                    ],
//                    [
//                        'attribute' => 'amount',
//                        'value' => function($model) {
//                            return $model->amount;
//                        }
//                    ],
//                    [
//                        'attribute' => 'status',
//                        'value' => function($model) {
//                            return Yii::$app->params['order']['status'][$model->status];
//                        }
//                    ],
//                    [
//                        'class' => yii\grid\ActionColumn::className(),
//                        'template' => '{view} {update} {delete}',
//                        'buttons' => [
//                            'view' => function($url, $model, $index) {
//                                return Html::tag('a', '', [
//                                            'href' => $url,
//                                            'title' => 'Просмотреть',
//                                            'aria-label' => 'Просмотреть',
//                                            'class' => 'fa fa-eye'
//                                ]);
//                            },
//                            'update' => function($url, $model, $index) {
//                                return Html::tag('a', '', [
//                                            'href' => $url,
//                                            'title' => 'Редактировать',
//                                            'aria-label' => 'Редактировать',
//                                            'class' => 'fa fa-pencil'
//                                ]);
//                            },
//                            'delete' => function($url, $model, $index) {
//                                return Html::tag('a', '', [
//                                            'href' => $url,
//                                            'title' => 'Удалить',
//                                            'aria-label' => 'Удалить',
//                                            'class' => 'fa fa-trash-o',
//                                            'style' => 'margin-left: 0;'
//                                ]);
//                            }
//                        ]
//                    ],
//                ],
            ]);
            ?>
        </div>
    </div>
</div>