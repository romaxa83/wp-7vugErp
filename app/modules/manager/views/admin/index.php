<?php
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\manager\models\RequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $store */

$this->title = 'Заявки для магазинов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-request-index content">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => [
            'class' => 'table-fix custom-table v3'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => 'Магазин',
                'format' => 'raw',
                'value' => 'store.firm',
            ],
            [
                'label' => 'Менеджер',
                'format' => 'raw',
                'value' => function($model){
                    return $model->store->name != null ? $model->store->name : '<b>Менеджера нет</b>';
                },
            ],
            [
                'label' => 'Статус',
                'format' => 'raw',
                'contentOptions' => function($model){
                    return ['class' => 'view-status-'.$model->store_id];
                },
                'value' => function($model){
                    if($model->status == 0){
                        $status = 'Заявка пустая';
                    }elseif($model->status == 1){
                        $status = 'Заявка сформирована ('.date("H:i:s d.m.Y",$model->updated_at) .')';
                    }else{
                        $status = 'В заявкe находяться товары , но заявка не сформирована';
                    }
                    return $status;
                }
            ],
            [
                'label' => 'Просмотр',
                'format' => 'raw',
                'value' => function($model){
                    return '<a 
                                href="/manager/admin/show?id='. $model->id .'"
                                title="Просмотреть и отредактировать заявку">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </a>';
                }
            ],
        ],
    ]); ?>
</div>