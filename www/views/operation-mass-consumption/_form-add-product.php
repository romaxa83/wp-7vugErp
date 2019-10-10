<?php 
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>
<?php $form = ActiveForm::begin(['action'=>'/operation-mass-consumption/add-product','id'=>'add-product-form']) ?>
<table id="table-add-prod" class="table table-striped consumption-mass"  data-currency-usd="<?=getUsd()?>">
    <thead>
    <tr>
        <th>Категория</th>
        <th>Выбрать товар</th>
        <th>На складе</th>
        <th>Кол-во</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <tr class="add-row-prod">
        <td class="add_cell_prod" style="width: 30%">
            <div class="form_group">
                <?=  
                    Html::dropDownList('category','',[],[
                        'class' => 'form-control product-category not-send',
                        'placeholder' => 'Выбрать категорию'
                    ]);
                ?>
            </div>
        </td>
        <td class="add-cell-prod choose-prod" style="width: 40%">
            <?= $form->field($operConsumption, 'product_id')->dropDownList([],['data-page' => 1])->label(false); ?>
            <div class="help-block"></div>
        </td>
        <td class="add-cell-prod stock-amount">
            <input type="number" class="form-control">
        </td>
        <td class="add-cell-prod consumption-amount" style="width: 15%">
            <div class="form-group">
                <?= $form->field($operConsumption, 'amount')->Input('float',['value' => 0,'min' => 0])->label(false) ?>
                <button id="trigger-mass-consumption" class="btn btn-success">OK</button>
            </div>
        </td>
        <td class="hidden">
            <?= $form->field($operConsumption,'transaction_id')->input('number',['value'=>Yii::$app->request->get('id')[0]])->label(false) ?>
        </td>
    </tr>
    </tbody>
</table>
<div id="product_variant_table"></div>
<?= Html::button('Добавить',['class' => 'snap add-mass-prod-consumption','style' => 'border:0;margin-bottom:40px'])?>
<?php ActiveForm::end(); ?>