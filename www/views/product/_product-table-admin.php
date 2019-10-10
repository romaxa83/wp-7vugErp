<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'showFooter' => false,
    'tableOptions' => [
        'class' => 'table-fix custom-table v3'
    ],
    'columns' => [
    ['class' => 'yii\grid\SerialColumn'],
    ['class' => 'yii\grid\CheckboxColumn',
        'cssClass'=>'mass-checked',
        'checkboxOptions'=>function ($model, $key, $index){
            return [
                'class' => 'custom-checkbox',
                'data-id' => $model->id,
                'checked' => $model->view_manager == 1 ? true : false
            ];
        },
        'contentOptions' => function($model, $key, $index, $column) {
            return [
                'class' => 'element-check check-view-product',
            ];
        },
        'header' => Html::checkBox(
        'selection_all',
        false,
        [
            'id' => 'selectAllElements',
            'type' => 'checkbox',
            'class' => 'custom-checkbox select-on-check-all'
        ]
        ),
        'headerOptions' => [
            'width' => '34',
            'class' => 'select-all-header'
        ]
    ],
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
        'value' => function($model){
            return $model->name;
        },
    ],
    [
        'attribute' => 'category',
        'format' => 'raw',
        'label' => 'Категория',
        'value' => function($model){
            $CategoryBlock = "<div class='life-edit-category' data-key='$model->id'>";
            $CategoryBlock .= "<span class='category-name'>{$model->category->name}</span>";
            $CategoryBlock .= "<select class='life-edit-category-body' data-key='$model->id'></select>";
            $CategoryBlock .= "</div>";
            return $CategoryBlock;
        }
    ],
    [
        'attribute' => 'agent',
        'label' => 'Поставщик',
        'format' => 'raw',
        'value' => function($model) use ($agents) {
            $un_agents = $model->additionalAgents;
            if (!empty($un_agents)){
                $items = [];
                foreach ($un_agents as $v) {
                    $items[] = (isset($agents[$v['agent_id']])) ? $agents[$v['agent_id']] : FALSE;
                }
                $rez = $model->agent->firm . Html::tag('span', '<i class="fa fa-info-circle fa-right"></i>', [
                    'title' => 'Дополнительные поставщики: ' . implode(', ', $items),
                    'data-toggle' =>'tooltip'
                ]);
            } else {
                $rez = $model->agent->firm;
            }
            return $rez;
        }
    ],
    [
        'attribute' => 'amount',
        'format' => 'raw',
        'value' => function($model){
            $percent = (20 * (int)$model->min_amount)/100;
            switch(true){
                case $model->min_amount > $model->amount : 
                    return '<span title="На складе товара меньше чем необходимо ('. $model->min_amount .')">'.$model->amount.'<i style="color: red" class="fa fa-exclamation-triangle fa-right"></i></span>';
                break;
                case $model->amount < ((int)$model->min_amount + (int)$percent) : 
                    return '<span title="Кол-во товара на складе приближаеться к минимальному ('. $model->min_amount .')">'.$model->amount.'<i style="color:gold" class="fa fa-exclamation-triangle fa-right""></i></span>';
                break;
                default : return $model->amount;
                break;
            }
        },
        'label' => 'Кол-во',
    ],
    'unit',
    [
        'attribute' => 'cost_price',
        'format' => 'raw',
        'value' => function($model){
            return number_format($model->cost_price,getFloat('usd'));
        },
        'label' => 'Себест($)',
    ],
    [
        'label' => 'Себест(uah)',
        'format' => 'raw',
        'value' => function($model){
            $price = number_format((float)$model->cost_price * (float)getUsd(),getFloat('ua'));
            return $price;
        }
    ],
    [
        'attribute' => 'start_price',
        'format' => 'raw',
        'value' => function($model){
            return number_format($model->start_price,getFloat('usd'));
        },
        'label' => 'П.Прих($)',
    ],
    [
        'attribute' => 'trade_price',
        'format' => 'raw',
        'value' =>function($model){
            return number_format($model->trade_price,getFloat('usd'));
        },
        'label' => 'Опт($)',
    ],
    [
        'attribute' => 'price1',
        'format' => 'raw',
        'value' => function($model){
            $percent = ((float)$model->margin * (float)$model->cost_price)/100;
            if ((float)$model->price1 <= ((float)$model->cost_price * (float)getUsd())){
                return '<span title="Цена1 меньше или равна цене прихода">'.number_format($model->price1,getFloat('ua')).'<i style="color: red" class="fa fa-exclamation-triangle fa-right"></i></span>';
            } elseif ((float)$model->price1 < ((float)getUsd() * ((float)$model->cost_price + $percent))){
                return '<span title="Цена1 меньше установленной маржи">'.number_format($model->price1,getFloat('ua')).'<i style="color: gold" class="fa fa-exclamation-triangle fa-right""></i></span>';
            } else {
                return number_format($model->price1,getFloat('ua'));
            }
        },
        'label' => 'P1(uah)',
    ],
    [
        'attribute' => 'price2',
        'format' => 'raw',
        'value' => function($model){
            $percent = ((float)$model->margin * (float)$model->cost_price)/100;
            if ((float)$model->price2 <= ((float)$model->cost_price * (float)getUsd())){
                return '<span title="Цена2 меньше или равна цене прихода">'.number_format($model->price2,getFloat('ua')).'<i style="color: red" class="fa fa-exclamation-triangle fa-right"></i></span>';
            } elseif ((float)$model->price2 <((float)getUsd() * ((float)$model->cost_price + (float)$percent))){
                return '<span title="Цена2 меньше установленной маржи">'.number_format($model->price2,getFloat('ua')).'<i style="color: gold" class="fa fa-exclamation-triangle fa-right""></i></span>';
            } else {
                return number_format($model->price2,getFloat('ua'));
            }
        },
        'label' => 'P2(uah)',
    ],
    [
        'label' => 'Вариации',
        'format' => 'raw',
        'value' => function($model){
            return $model->is_variant == 2 ? '<span class="show-var-prod icon-var-product" data-id-product="'. $model->id .'" title="Показать вариативные товары"><i class="fa fa-eye" aria-hidden="true"></i></span>' : '' ;
        }
    ],
    ['class' => 'yii\grid\CheckboxColumn',
        'cssClass'=>'',
        'checkboxOptions'=>function ($model, $key, $index){
            return [
                'class' => 'custom-checkbox publish_status',
                'data-id' => $model->id,
                'data-type' => 'product',
                'checked' => $model->publish_status == 1 ? true : false,
                'disabled' => ($model->status == 1 && $model['category']->status == 1) ? false : true ,
                'title' => 'Статус для магазина'
            ];
        },
        'contentOptions' => function($model, $key, $index, $column) {
            return [
                'class' => 'element-check',
            ];
        },
        'header' => '',
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{update} {delete}',
        'buttons' => [
            'update' => function ($url, $model) {
                return Html::a('<i class="fa fa-pencil"></i>', Url::toRoute(['update', 'id' => $model->id]), [
                                'title' => Yii::t('yii', 'Update'),
                                'aria-label' => Yii::t('yii', 'Update'),
                                'data-pjax' => '0',
                            ]);
                },
            'delete' => function ($url, $model) {
                $checked = $model->status == 1 ? 'checked' : '';
                return '<input id="product-status_'. $model->id .'" class="change-status-product" type="checkbox" '. $checked .' data-id="'. $model->id .'"><label for="product-status_'. $model->id .'" class="status-product" data-text-true="on" data-text-false="off"><i></i></label>';
                }
            ]
        ],
    ],
]); 
