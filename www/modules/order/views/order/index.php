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
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => [
                    'class' => 'custom-table v3 table-fix'
                ],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'order',
                        'value' => function($model) {
                            return $model->order;
                        }
                    ],
                    [
                        'attribute' => 'date',
                        'value' => function($model) {
                            return Yii::$app->formatter->asDate($model->date, 'php:d.m.Y H:i:s');
                        }
                    ],
                    [
                        'attribute' => 'amount',
                        'value' => function($model) {
                            return number_format($model->amount, 2, '.', '');
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'value' => function($model) {
                            return Yii::$app->params['order']['status'][$model->status];
                        }
                    ],
                    [
                        'class' => yii\grid\ActionColumn::className(),
                        'template' => '{view} {delete}',
                        'buttons' => [
                            'view' => function($url, $model, $index) {
                                return Html::tag('a', '', [
                                            'href' => Url::toRoute(['view', 'id' => $model->order]),
                                            'title' => 'Просмотреть',
                                            'aria-label' => 'Просмотреть',
                                            'class' => 'fa fa-eye'
                                ]);
                            },
                            'delete' => function($url, $model, $index) {
                                return Html::tag('a', '', [
                                            'href' => Url::toRoute(['delete', 'id' => $model->order]),
                                            'title' => 'Удалить',
                                            'aria-label' => 'Удалить',
                                            'class' => 'fa fa-trash-o',
                                            'style' => 'margin-left: 0;'
                                ]);
                            }
                        ]
                    ],
                ],
            ]);
            ?>
        </div>
    </div>
</div>