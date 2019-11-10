<?php

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel lav45\activityLogger\modules\models\ActivityLogSearch
 */
use yii\grid\GridView;

$this->title = Yii::t('lav45/logger', 'Activity log');
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="content" id="activity_log">
    <div class="row">
        <div class="col-md-12">
            <?php //Pjax::begin(); ?>
            <?php echo $this->render('_search', ['model' => $searchModel,'countPage'=>$countPage]); ?>
            <?php
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => [
                    'class' => 'custom-table v3 table-fix'
                ],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'transaction',
                        'label' => 'Пользователь',
                        'format' => 'raw',
                        'value' => function($model) {
                            return $model->getUserName();
                        }
                    ],
                    [
                        'attribute' => 'transaction',
                        'label' => 'Модель',
                        'format' => 'raw',
                        'value' => function($model) {
                            return $model->getEntityName();
                        }
                    ],                            [
                        'attribute' => 'transaction',
                        'label' => 'Id модели',
                        'format' => 'raw',
                        'value' => function($model) {
                            return $model->entity_id;
                        }
                    ],
                    [
                        'attribute' => 'transaction',
                        'label' => 'Действие',
                        'format' => 'raw',
                        'value' => function($model) {
                            return Yii::t('lav45/logger', $model->action);
                        }
                    ],
                    [
                        'attribute' => 'transaction',
                        'label' => 'Дата',
                        'format' => 'raw',
                        'value' => function($model) {
                            $formatter = Yii::$app->formatter;
                            return '<b>' . $formatter->asRelativeTime($model->created_at) . '</b><br><span>'.$formatter->asDatetime($model->created_at).'</span>';
                        }
                    ],
                    [
                        'attribute' => 'transaction',
                        'label' => 'Информация',
                        'format' => 'raw',
                        'value' => function($model) {
                            $data = '<ul class="details">';
                            foreach ($model->getData() as $attribute => $values) {
                                if (is_string($values)) {
                                    $data .= '<li>';
                                    if (is_numeric($attribute) || empty($attribute)) {
                                        $data .= $values;
                                    } else {
                                        $data .= '<strong>' . $attribute . '</strong> ' . $values;
                                    }
                                    $data .= '</li>';
                                } else {
                                    $data .= '<li>';
                                    $data .= Yii::t('lav45/logger', '<strong>{attribute}</strong> has been changed', ['attribute' => $attribute]);
                                    $data .= ' ' . Yii::t('lav45/logger', 'from');
                                    $data .= '<strong><i class="details-text"> ' . $values->getOldValue() . ' </i></strong>';
                                    $data .= Yii::t('lav45/logger', 'to');
                                    $data .= '<strong><i class="details-text"> ' . $values->getNewValue() . ' </i></strong>';
                                    $data .= '</li>';
                                }
                            }
                            $data .= '</ul>';
                            return $data;
                        }
                    ]
                ],
            ]);
            ?>
            <?php //Pjax::end(); ?>
        </div>
    </div>
</section>