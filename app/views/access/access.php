<?php

use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Управление доступом';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="operations-index content">
    <div class="row">
        <div class="col-md-2">
            <a href="<?php echo Url::toRoute(['/access/add-access']); ?>" class="snap">Создать правило</a>     
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => [
                    'class' => 'custom-table v3'
                ],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'transaction',
                        'label' => 'Название',
                        'format' => 'raw',
                        'value' => function($model) {
                            return ($model->weight == 0) ? $model->name : '⚫ ' . $model->name;
                        }
                    ],
                    [
                        'attribute' => 'repository',
                        'label' => 'Контроллер',
                        'format' => 'raw',
                        'value' => function($model) {
                            return $model->controller;
                        }
                    ],
                    [
                        'attribute' => 'store',
                        'label' => 'Действие',
                        'format' => 'raw',
                        'value' => function($model) {
                            return $model->action;
                        }
                    ],
                    [
                        'attribute' => 'store',
                        'label' => 'Статус',
                        'format' => 'raw',
                        'value' => function($model) {
                            $checked = ($model->status == 1) ? 'checked' : '';
                            return '<input class="сontrol-access-status" type="checkbox" ' . $checked . ' data-id="' . $model->id . '">';
                        }
                    ],
                    ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {delete}'],
                ],
            ]);
            ?>
        </div>
    </div>
</div>