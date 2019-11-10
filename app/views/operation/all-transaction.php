<?php


/* @var $operations */
/* @var $pages */

use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Всё транзакций';
$this->params['breadcrumbs'][] = ['label' => 'Перемещение товара', 'url' => ['/operations/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12">
        <div class="row">
            <div class="col-xs-6">
                <?= $this->render('_search',['model'=>$model]); ?>
            </div>
            <div class="text-right control-btn col-xs-6">
                <div class="show-archive-panel">
                    <?= Html::button('Архив',['class' => 'snap mass-archive'])?>
                </div>
                <div  class="panel-archive">
                    <?= Html::button('Подтвердить',['class' => 'snap send-mass-archive'])?>
                    <?= Html::button('Отменить',['class' => 'snap _gray cancel-mass-archive'])?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="table">
                    <?php 
                        echo GridView::widget([
                            'dataProvider' => $dataProvider,
                            'tableOptions' => [
                                'class' => 'table-fix info-trans-table custom-table v3'
                            ],
                            'rowOptions'=>function($model){
                                if($model['status'] == 2){
                                    return ['class' => 'red-text'];
                                }
                            },
                            'columns' => [
                                [
                                    'header' => '№ Транзакции',
                                    'value' => function($model){
                                        return $model['transaction'];
                                    }
                                ],
                                [
                                    'header' => 'Откуда',
                                    'value' => function($model){
                                        return $model['whenceagent']['firm'];
                                    }
                                ],
                                [
                                    'header' => 'Куда',
                                    'value' => function($model){
                                        return $model['whereagent']['firm'];
                                    }
                                ],
                                [
                                    'header' => 'Дата',
                                    'value' => function($model){
                                        return $model['date'];
                                    }
                                ],
                                [
                                    'header' => 'Тип',
                                    'value' => function($model){
                                        if($model['type'] == 1){
                                            return 'Приход';
                                        }elseif($model['type'] == 2){
                                            return 'Расход';
                                        }elseif($model['type'] == 3){
                                            return 'Корректировка';
                                        }      
                                    }
                                ],
                                [
                                    'header' => 'Сумма($) опт',
                                    'value' => function($model){
                                        return number_format($model['trade_price'], getFloat('usd'));
                                    }
                                ],
                                [
                                    'header' => 'Сумма(UAH)',
                                    'value' => function($model){
                                        return number_format($model['total_ua'], getFloat('uah'));
                                    }
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'template' => '{update} {view} {print} {close} {send-archive} {archive-checkbox}',
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
                                        'view' => function ($model) {
                                            return Html::a('<span class="fa fa-eye"></span>', '/operation/get-transaction-table', [
                                                'title' => Yii::t('app', 'просмотр транзакций'),
                                                'class' => 'get-transaction-table'
                                            ]);
                                        },
                                        'print' => function ($url,$model) {
                                            if($model['type'] == 1){
                                                $url = '/operation-coming';
                                            }
                                            if($model['type'] == 2){
                                                $url = '/operation-consumption';
                                            }
                                            if($model['type'] == 3){
                                                return ;
                                            }
                                            return Html::a('<span class="fa fa-print"></span>', 
                                                $url . '/print-pdf?id='.$model['id'], [
                                                'title' => Yii::t('app', 'печать транзакций'),
                                                'target'=>'_blank'
                                            ]);
                                        },
                                        'close' => function ($url,$model) {
                                            if($model['status'] != 2){
                                                return Html::a('<span class="fa fa-check"></span>', $url, [
                                                    'title' => Yii::t('app', 'запрет на редактирования транзакций'),
                                                    'class' => 'close-transaction'
                                                ]);
                                            }
                                        },
                                        'send-archive' => function ($url,$model) {
                                            return Html::a('<span class="fa fa-archive"></span>', '#', [
                                                'title' => Yii::t('app', 'отправка в архив транзакций'),
                                                'data-id' => $model['id'],
                                                'class' => 'send-archive-one'
                                            ]);
                                        },
                                        'archive-checkbox' => function(){
                                            return Html::checkbox('archive-checkbox', false,[
                                                'title' => Yii::t('app', 'отправка в архив транзакций'),
                                                'class' => 'mass-archive-checkbox',
                                                'style' => 'display:none'
                                            ]);
                                        }
                                  ],
                                ],
                            ],
                        ]);
                    ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="transaction-info col-sm-12">

            </div>
        </div>
    </div>
</div>
