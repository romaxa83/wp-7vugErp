<?php 
    use yii\widgets\ActiveForm;
    use kartik\date\DatePicker;
    use kartik\select2\Select2;
    use yii\web\JsExpression;
    $form = ActiveForm::begin();
?>
<div class="content">
    <div class="row">
        <div class="col-md-2">
            <?=
                $form->field($model, 'name')->widget(Select2::classname(), [
                    'initValueText' => $product[0]['name'] ?? [],
                    'options' => ['placeholder' => 'Введите название товара или артикул', 'class'=>'settlement-select'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'language' => [
                            'errorLoading' => new JsExpression("function () { return 'Загрузка результатов'; }"),
                        ],
                        'ajax' => [
                            'url' => '/product/all-product',
                            'async' => false,
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {name:params.term}; }')
                        ],
                        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new JsExpression('function(city) { return city.text; }'),
                        'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                    ],
                ])->label(false); 
            ?>
        </div>
        <div class="col-md-2">
            <?=  $form->field($model,'start_data')->textInput()->label(false)->widget(DatePicker::className(), [
                'name' => 'event_time',
                'language' => 'ru',
                'type' => DatePicker::TYPE_INPUT,
                'options' => ['placeholder' => 'От', 'autocomplete' => 'off'],
                'convertFormat' => true,
                'pluginOptions' => [
                    'format' => 'dd.MM.yyyy',
                    'autoclose' => true,
                    'weekStart' => 1,
                    'todayBtn' => true,
                ]
            ]); ?>
        </div>
        <div class="col-md-2">
            <?=  $form->field($model,'end_data')->textInput()->label(false)->widget(DatePicker::className(), [
                'name' => 'event_time',
                'language' => 'ru',
                'type' => DatePicker::TYPE_INPUT,
                'options' => ['placeholder' => 'До', 'autocomplete' => 'off'],
                'convertFormat' => true,
                'pluginOptions' => [
                    'format' => 'dd.MM.yyyy',
                    'autoclose' => true,
                    'weekStart' => 1,
                    'todayBtn' => true,
                ]
            ]); ?>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success">Фильтр</button>
            <button class="btn btn-success reset-filter">Сброс</a>
        </div>
    </div>
</div>
<?php ActiveForm::end();