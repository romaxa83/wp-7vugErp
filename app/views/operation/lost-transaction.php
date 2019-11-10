<?php
use yii\helpers\Html;
use yii\grid\GridView;
?>
<div>
    <label class="control-label">Список открытых транзакций.</label>
    <?= 
        GridView::widget([
            'dataProvider' => $lost_transaction,
            'tableOptions' => [
                'class' => 'table-fix info-trans-table custom-table v3'
            ],
            'columns' => [
                [
                    'header' => '№ Транзакции',
                    'value' => function($model){
                        return $model['transaction'];
                    }
                ],
                [
                    'header' => Yii::$app->controller->id === 'operation-coming' ? 'Поставщик' : 'Магазин',
                    'value' => function($model){
                        return isset($model['whence']) ? $model['whenceagent']['firm'] : $model['whereagent']['firm'] ;
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update}{print}{close}',
                    'buttons' => [
                        'update' => function ($url, $model) {
                            if($model['type'] != 3 && $model['status'] != 2){
                                return Html::a('<span class="fa fa-pencil"></span>',
                                    $model['type'] == 2 ? '/operation-consumption/show?id='.$model['id'] : '/operation-coming/show?id='.$model['id']
                                    ,[
                                        'title' => Yii::t('app', 'редактирования транзакций'),
                                    ]);
                            }
                        },
                        'print' => function ($url,$model) {
                            if($model['type'] == 1){
                                $url = '/operation-coming';
                            }
                            if($model['type'] == 2){
                                $url = '/operation-consumption';
                            }
                            if($model['type'] == 3){
                                $url = '/operation-adjusment';
                            }
                            return Html::a('<span class="fa fa-print"></span>', 
                                $url . '/print-pdf?id='.$model['id'], [
                                'title' => Yii::t('app', 'печать транзакций'),
                                'target'=>'_blank',
                            ]);
                        },
                        'close' => function ($url,$model) {
                            if($model['status'] != 2){
                                return Html::a('<span class="fa fa-check"></span>', $url, [
                                    'title' => Yii::t('app', 'запрет на редактирования транзакций'),
                                    'class' => 'close-transaction'
                                ]);
                            }
                        }
                    ],    
                ],
            ],
        ]);
    ?>
</div>