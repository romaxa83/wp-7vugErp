<?php 
    echo yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'showFooter' => FALSE,
        'columns' => [
            [
                'label' => 'Имя',
                'value' => function($model){
                    return $model['name'];
                }
            ],
            [
                'label' => 'Артикул',
                'value' => function($model){
                    return $model['vendor_code'];
                }
            ],
            [
                'label' => 'Категория',
                'value' => function($model){
                    return $model['category']['name'];
                }
            ],
            [
                'label' => 'Поставщик',
                'value' => function($model){
                    return $model['agent']['firm'];
                }
            ],
            [
                'label' => 'Кол-во',
                'value' => function($model){
                    return $model['amount'];
                }
            ],
            [
                'label' => 'Измерения',
                'value' => function($model){
                    return $model['unit'];
                }
            ],
            [
                'label' => 'Себест($)',
                'value' => function($model){
                    return formatedPriceUSD($model['cost_price']);
                }
            ],
            [
                'label' => 'П.Прих($)',
                'value' => function($model){
                    return formatedPriceUSD($model['start_price']);
                }
            ],
            [
                'label' => 'Опт($)',
                'value' => function($model){
                    return formatedPriceUSD($model['trade_price']);
                }
            ],
            [
                'label' => 'Цена1(UAH)',
                'value' => function($model){
                    return formatedPriceUA($model['price1']);
                }
            ],
            [
                'label' => 'Цена2(UAH)',
                'value' => function($model){
                    return formatedPriceUA($model['price2']);
                }
            ],
        ]
    ]);