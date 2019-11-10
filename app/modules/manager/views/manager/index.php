<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;
use app\modules\manager\ManagerAsset;

$this->title = 'Заявка менеджера';
$this->params['breadcrumbs'][] = $this->title;

ManagerAsset::register($this);
?>
<section class="content">

    <div class="col-sm-12 text-right">
        <div class="imex">
            <?= Html::a('Открыть Заявку',
                [Url::toRoute(['/manager/manager/show-request'])],
                ['class' => 'snap', 'title' => 'Создать заявку товаров для магазина'])
            ?>
        </div>
    </div>
    <div class="product-for-manager collapse <?= empty(Yii::$app->request->get('page')) ? '' : 'in' ?>">
        <?php
        echo GridView::widget([
            'dataProvider' => $dataProviderManager,
            'showFooter' => false,
            'tableOptions' => [
                'class' => 'custom-table v3'
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
                        return Yii::$app->session->get('getStorePrice') == 1 ?
                            number_format($model->price1,getFloat('ua')):
                            number_format($model->price2,getFloat('ua'));
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
    </div>

</section>