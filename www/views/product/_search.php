<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;

?>
<?php $form = ActiveForm::begin([
    'id' => 'custom-filter',
    'action' => Url::toRoute('product/index'),
    'method' => 'GET'
]); ?>
<div class="row">
    <div class="col-sm-6">
        <?= $form->field($searchModel,'name')->widget(Select2::classname(), [
                'initValueText' => $searchModel->name, // set the initial display text
                'options' => [
                    'placeholder' => 'Поиск по имени товара ...'
                ],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 3,
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Ожидаем список товаров ...'; }"),
                ],
                'ajax' => [
                    'url' => '/product/all-product',
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {name:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression('function (result) { return result; }'),
                'templateResult' => new JsExpression('function(result) { return result.text; }'),
                'templateSelection' => new JsExpression('function (result) { return result.text; }'),
            ]
        ])->label(false); ?>
    </div>
    <div class="col-sm-6">
        <div class="form-group field-agent_filter">
            <?= $form->field($searchModel,'agent')->dropDownList($agents_filter,
                ['id' => 'agent_filter','prompt' => 'Поставщиики']
            )->label(false);?>
        </div>
        <div class="form-group field-productsearch-warning">
            <?= $form->field($searchModel, 'warning')->checkbox(['class' => 'product-warning']); ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <?= $form->field($searchModel,'category')->dropDownList([],
            ['id' => 'category_filter','prompt' => 'Категории','data-value' => $searchModel->category]
        )->label(false);?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($searchModel, 'created_at')->label(false)->widget(DatePicker::className(),[
            'name' => 'event_time',
            'language' => 'ru',
            'type' => DatePicker::TYPE_INPUT,
            'options' => ['placeholder' => 'Дата'],
            'convertFormat' => true,
            'pluginOptions' => [
                'format' => 'dd.MM.yyyy',
                'autoclose'=>true,
                'weekStart'=>1, //неделя начинается с понедельника
                'todayBtn'=>true, //снизу кнопка "сегодня"
            ]
        ]); ?>
    </div>
</div>
<div class="row button-controle-filter">
    <div class="col-sm-2 col-lg-1">
        <input type="submit" class="snap" title="Фильтр" value="Фильтр">
    </div>
    <div class="col-sm-2 col-lg-1">
        <?= HTML::a('Сброс',Url::toRoute('/product/index'),['class' => 'reset-filter snap','title' => 'Сброс'])?>
    </div>
    <div class="col-sm-2 col-lg-1">
        <?= Html::button('Отмена',['class' => 'hide-panel snap _gray', 'title' => 'Отменить'])?>
    </div>
</div>
<?php ActiveForm::end(); ?>