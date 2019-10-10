<?php
    use yii\grid\GridView;
?>
<div class="content">
    <?php
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'showFooter' => false,
            'summary' => false,
            'tableOptions' => [
                'class' => 'table-fix custom-table v3'
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'vendor_code',
                [
                    'attribute' => 'created_at',
                    'label' => 'Дата',
                    'format' => ['date', 'php:d.m.Y']
                ],
                [
                    'attribute' => 'name',
                    'label' => 'Товар',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->name;
                    },
                ],
                [
                    'attribute' => 'category',
                    'label' => 'Категория',
                    'value' => 'category.name',
                ],
                [
                    'attribute' => 'agent',
                    'label' => 'Поставщик',
                    'value' => 'agent.firm',
                ],
                [
                    'attribute' => 'amount',
                    'format' => 'raw',
                    'contentOptions' => [
                        'class' => 'trafic-amount'
                    ],
                    'value' => function($model) {
                        Yii::$app->session->set('trafic_product_amount', $model->amount);
                        $percent = (20 * (int) $model->min_amount) / 100;
                        if ($model->min_amount >= $model->amount) {

                            return '<span title="На складе товара меньше чем необходимо (' . $model->min_amount . ')">' . $model->amount . '<i style="color: red" class="fa fa-exclamation-triangle fa-right"></i></span>';
                        } elseif ($model->amount < ((int) $model->min_amount + (int) $percent)) {

                            return '<span title="Кол-во товара на складе приближаеться к минимальному (' . $model->min_amount . ')">' . $model->amount . '<i style="color:gold" class="fa fa-exclamation fa-right"></i></span>';
                        } else {

                            return $model->amount;
                        }
                    },
                    'label' => 'Кол-во',
                ],
                'unit',
                [
                    'attribute' => 'cost_price',
                    'format' => 'raw',
                    'value' => function($model) {
                        return number_format($model->cost_price, getFloat('usd'));
                    },
                    'label' => 'Себест($)',
                ],
                [
                    'label' => 'Себест(uah)',
                    'format' => 'raw',
                    'value' => function($model) {
                        $price = number_format((float) $model->cost_price * (float) getUsd(), getFloat('ua'));
                        return $price;
                    }
                ],
                [
                    'attribute' => 'start_price',
                    'format' => 'raw',
                    'value' => function($model) {
                        return number_format($model->start_price, getFloat('usd'));
                    },
                    'label' => 'П.Прих($)',
                ],
                [
                    'attribute' => 'trade_price',
                    'format' => 'raw',
                    'value' => function($model) {
                        return number_format($model->trade_price, getFloat('usd'));
                    },
                    'label' => 'Опт($)',
                ],
                [
                    'attribute' => 'price1',
                    'format' => 'raw',
                    'value' => function($model) {
                        $percent = ((float) $model->margin * (float) $model->cost_price) / 100;

                        if ((float) $model->price1 <= ((float) $model->cost_price * (float) getUsd())) {

                            return '<span title="Цена1 меньше или равна цене прихода">' . number_format($model->price1, getFloat('ua')) . '<i style="color: red" class="fa fa-exclamation-triangle"></i></span>';
                        } elseif ((float) $model->price1 < ((float) getUsd() * ((float) $model->cost_price + $percent))) {

                            return '<span title="Цена1 меньше установленной маржи">' . number_format($model->price1, getFloat('ua')) . '<i style="color: gold" class="fa fa-exclamation"></i></span>';
                        } else {
                            return number_format($model->price1, getFloat('ua'));
                        }
                    },
                    'label' => 'P1(uah)',
                ],
                [
                    'attribute' => 'price2',
                    'format' => 'raw',
                    'value' => function($model) {
                        $percent = ((float) $model->margin * (float) $model->cost_price) / 100;
                        if ((float) $model->price2 <= ((float) $model->cost_price * (float) getUsd())) {

                            return '<span title="Цена2 меньше или равна цене прихода">' . number_format($model->price2, getFloat('ua')) . '<i style="color: red" class="fa fa-exclamation-triangle"></i></span>';
                        } elseif ((float) $model->price2 < ((float) getUsd() * ((float) $model->cost_price + (float) $percent))) {

                            return '<span title="Цена2 меньше установленной маржи">' . number_format($model->price2, getFloat('ua')) . '<i style="color: gold" class="fa fa-exclamation"></i></span>';
                        } else {
                            return number_format($model->price2, getFloat('ua'));
                        }
                    },
                    'label' => 'P2(uah)',
                ]
            ]
        ]);
    ?>
</div>