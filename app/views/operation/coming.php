<?php


/* @var $operations */
/* @var $pages */

use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Приход товара';
$this->params['breadcrumbs'][] = ['label' => 'Перемещение товара', 'url' => ['/operations/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12">
        <div class="text-right control-btn">
            <div class="show-archive-panel">
                <?= Html::button('Архив',['class' => 'snap mass-archive'])?>
            </div>
            <div  class="panel-archive">
                <?= Html::button('Подтвердить',['class' => 'snap send-mass-archive'])?>
                <?= Html::button('Отменить',['class' => 'snap _gray cancel-mass-archive'])?>
            </div>
        </div>
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
                            'header' => 'Сумма(UAH)',
                            'value' => function($model){
                                return number_format($model['total_ua'], getFloat('uah'));
                            }
                        ],
                        [
                            'header' => 'Сумма(USD)',
                            'value' => function($model){
                                return number_format($model['total_usd'], getFloat('usd'));
                            }
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{update} {view} {print} {close} {send-archive} {archive-checkbox}',
                            'buttons' => [
                                'update' => function ($url, $model) {
                                    if($model['status'] != 2){
                                        return Html::a('<span class="fa fa-pencil"></span>',
                                            '/operation-coming/show?id='.$model['id']
                                        ,[
                                            'title' => Yii::t('app', 'редактирования транзакций'),
                                        ]);
                                    }
                                },
                                'view' => function ($url) {
                                    return Html::a('<span class="fa fa-eye"></span>', '/operation/get-transaction-table', [
                                        'title' => Yii::t('app', 'просмотр транзакций'),
                                        'class' => 'get-transaction-table'
                                    ]);
                                },
                                'print' => function ($url,$model) {
                                    return Html::a('<span class="fa fa-print"></span>', 
                                        '/operation-coming/print-pdf?id='.$model['id'], [
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
