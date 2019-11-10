<?php

use app\models\Agent;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Архив транзакций';
$this->params['breadcrumbs'][] = ['label' => 'Перемещение товара', 'url' => ['/operations/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="operation-archive content">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => [
            'class' => 'table-fix custom-table v3'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => 'Транзакция',
                'format' => 'raw',
                'value' => function($model){
                    return $model['transaction'];
                }
            ],
            [
                'label' => 'Тип',
                'format' => 'raw',
                'value' => function($model){
                    if($model['type'] == 1){
                        return 'Приход';
                    }
                    if($model['type'] == 2){
                        return 'Расход';
                    }
                    if($model['type'] == 3){
                        return 'Корректировка';
                    }
                }
            ],
            [
                'label' => 'Откуда',
                'format' => 'raw',
                'value' => function($model){
                    return $model['whence']['firm'];
                }
            ],
            [
                'label' => 'Куда',
                'format' => 'raw',
                'value' => function($model){
                    return $model['where']['firm'];
                }
            ],
            [
                'label' => 'Дата созд. транзакции',
                'format' => 'raw',
                'value' => function($model){
                    return $model['date'];
                }
            ],
            [
                'label' => 'Дата архивации транзакции',
                'format' => 'raw',
                'value' => function($model){
                    return $model['date_archive'];
                }
            ],
            [
                'label' => 'Просмотр',
                'format' => 'raw',
                'value' => function($model){
                    return Html::a('<span class="fa fa-eye"></span>', '/operation/get-archive-table', [
                        'title' => Yii::t('app', 'просмотр транзакций'),
                        'class' => 'get-transaction-table'
                    ]);
                }
            ],
        ],
    ]); ?>
    <div class="row">
        <div class="transaction-info col-sm-12">

        </div>
    </div>
</div>
