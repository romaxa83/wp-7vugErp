<?php

use yii\helpers\Url;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
?>
<div class="operations-search">
<?php $form = ActiveForm::begin([
        'id' => 'filter-operations',
        'action' => Url::toRoute('operation/all-transaction'),
        'method' => 'GET'
    ]); ?>
    <div style="display: -webkit-inline-box;">
        <?= $form->field($model,'transaction')->textInput(['placeholder' => 'Номер транзакции'])->label(false)?>
        <?= $form->field($model,'type')->widget(Select2::classname(), [
                'data' => [
                    1 => 'Приход',
                    2 => 'Расход',
                    3 => 'Корректировка'
                ],
                'language' => Yii::$app->language,
                'options' => [
                    'placeholder' => 'Вибрать тип',
                    'class' => 'form-control',
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label(false); ?>
        <?= $form->field($model, 'date')->label(false)->widget(DatePicker::className(),[
            'name' => 'event_time',
            'language' => 'ru',
            'type' => DatePicker::TYPE_INPUT,
            'options' => ['placeholder' => 'Дата','style' => 'margin-left:20px'],
            'convertFormat' => true,
            'pluginOptions' => [
                'format' => 'dd.MM.yyyy',
                'autoclose'=>true,
                'weekStart'=>1, //неделя начинается с понедельника
                'todayBtn'=>true, //снизу кнопка "сегодня"
            ]
        ]); ?>
        <?= HTML::submitButton('Фильтр',['class' => 'snap'])?>
        <?= HTML::a('Сброс',Url::toRoute('/operations/index'),['class' => 'snap reset-filter','title' => 'Сброс'])?>
    </div>
    <?php ActiveForm::end(); ?>
</div>