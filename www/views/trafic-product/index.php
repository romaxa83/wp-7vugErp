<?php 
use yii\helpers\Html;
$this->title = 'Перемещения товара';
    echo $this->render('_form_search',['model' => $model,'product' => empty($dataProvider) ? [] : $dataProvider->getModels()]);
    if(!empty($dataProvider)){
        echo $this->render('product-table',['dataProvider' => $dataProvider]); 
    }
?>
<div class="content">
    <?php 
        if(!empty($productHistory) && $productHistory->getTotalCount() > 0) :
            echo yii\grid\GridView::widget([
                'dataProvider' => $productHistory,
                'showFooter' => false,
                'summary' => false,
                'tableOptions' => [
                    'class' => 'table-fix custom-table v3'
                ],
                'columns' => [
                    [
                        'attribute' => 'Дата',
                        'value' => function($model){
                            return $model['date'];
                        }
                    ],
                    [
                        'attribute' => 'Транзакция',
                        'value' => function($model){
                            return $model['transaction'];
                        }
                    ],
                    [
                        'attribute' => 'Движения',
                        'value' => function($model){
                            return $model['type'];
                        }
                    ],
                    [
                        'attribute' => 'Поставщик',
                        'value' => function($model){
                            return $model['agent'];
                        }
                    ],
                    [
                        'attribute' => 'Остаток',
                        'value' => function($model,$key) use ($balance){
                            if($model['type'] == 'приход'){
                                $balance[$key] -= $model['row']->amount;
                            }elseif($model['type'] == 'расход') {
                                $balance[$key] += $model['row']->amount;
                            }
                            return $balance[$key];
                        }
                    ],
                    [
                        'attribute' => 'Кол-во',
                        'value' => function($model){
                            return $model['row']->amount;
                        }
                    ],                       
                    [
                        'attribute' => 'Итого',
                        'value' => function($model,$key) use ($balance){
                            return $balance[$key];
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{print}',
                        'buttons' => [
                            'print' => function ($url, $model,$key) {
                                if($model['row']::className() == 'app\models\ArchiveValue'){
                                    return '<span class="fa red" aria-hidden="true">архив</span>';
                                }
                                $url = '/operation-coming/print-pdf?id='.$model['row']->transaction_id;
                                if($model['type'] == 'расход'){
                                    $url = '/operation-consumption/print-pdf?id='.$model['row']->transaction_id;
                                }elseif($model['type'] == 'коректировка'){
                                    return '<span class="fa red" aria-hidden="true">коректировка</span>';
                                }
                                return Html::a('<span class="fa fa-print" aria-hidden="true"></span>',$url,['target'=>'_blank']);
                            },
                        ],
                    ]
                ]
            ]);
        elseif(!empty($model->name)) : ?>
            <div>
                <h3>История не найдена</h3>
            </div>
        <?php endif; ?>
</div>