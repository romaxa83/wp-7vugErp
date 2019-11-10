<?php
use yii\grid\GridView;

/* @var $dataProviderManager yii\data\ActiveDataProvider */
?>


<?php

echo GridView::widget([
    'dataProvider' => $dataProviderManager,
    'showFooter' => false,
    'tableOptions' => [
        'class' => 'table-fix custom-table v3'
    ],
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'options' => ['style' => 'width: 50px']
        ],
        [
            'attribute' => 'name',
            'label' => 'Товар',
            'format' => 'raw',
            'value' => function($model){
                return $model->name;
            },
            'headerOptions' => ['style' => 'width: 150px']
        ],
        [
            'attribute' => 'category',
            'label' => 'Категория',
            'value' => 'category.name',
            'headerOptions' => ['style' => 'width: 150px']
        ],
        [
            'label' => 'Цена(uah)',
            'format' => 'raw',
            'value' => function($model){
                return Yii::$app->session->get('getStorePrice') == 1 ?$model->price1:$model->price2;
            },
            'headerOptions' => ['style' => 'width: 100px']
        ],
        [
            'label' => 'Вариации',
            'format' => 'raw',
            'value' => function($model){
                return $model->is_variant == 2 ? '<span class="show-var-prod icon-var-product" data-id-product="'. $model->id .'" title="Показать вариативные товары"><i class="fa fa-eye" aria-hidden="true"></i></span>' : '' ;
            },
            'headerOptions' => ['style' => 'width:100px']
        ],
    ],
]); ?>
