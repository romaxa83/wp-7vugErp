<?php 
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$form = ActiveForm::begin(['id'=>'add-product-form','action'=>'/operation-consumption/add-product']); ?>
<table id="table-add-prod" class="table table-striped consumption"  data-currency-usd="<?=getUsd()?>">
    <thead>
    <tr>
        <th width="30%">Категория</th>
        <th width="40%">Выбрать товар</th>
        <th width="10%">На складе</th>
        <th width="10%">Кол-во</th>
        <th width="10%">Цена(uah)</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <tr class="add-row-prod">
        <td>
            <?=  
                Html::dropDownList('category','',[],[
                    'class' => 'form-control product-category not-send',
                    'placeholder' => 'Выбрать категорию'
                ]);
            ?>
        </td>
        <td>
            <div class="form-group choose-prod" data-type="true">
                <?= $form->field($model, 'product_id')->dropDownList([],['data-page' => 1])->label(false); ?>
                <div class="help-block"></div>
            </div>
        </td>
        <td class="add-cell-prod stock-amount">
            <input type="number" class="form-control">
        </td>
        <td class="add-cell-prod consumption-amount">
            <?= $form->field($model, 'amount')->input('number',['min' => 0])->label(false) ?>
            <button id="trigger-consumption" class="btn btn-success" style="">OK</button>
        </td>
        <td class="add-cell-prod">
            <?= $form->field($model, 'price')->Input('float',['value' => 0,'min' => 0])->label(false) ?>
        </td>
        <td class="hidden">
            <?= $form->field($model,'transaction_id')->input('number',['value'=>Yii::$app->request->get('id')])->label(false) ?>
        </td>
    </tr>
    </tbody>
</table>
<div id="product_variant_table"></div>
<?= Html::submitButton('Добавить',['class' => 'add-prod snap add-prod-consumption','style' => 'border:0;margin-bottom:40px'])?>
<?php ActiveForm::end();