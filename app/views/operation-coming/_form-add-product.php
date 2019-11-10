<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
?>

<?php $form = ActiveForm::begin(['action' => '/operation-coming/add-product', 'id' => 'add-product-form']);?>
    <div class="table-form-product" style="margin-top: 40px">

        <table id="table-add-prod" class="table table-striped" data-currency-usd="<?=getUsd()?>"  data-float-ua="<?=getFloat('ua')?>" data-float-usd="<?=getFloat('usd')?>">
            <thead>
            <tr>
                <th width="30%">Категория</th>
                <th width="30%">Выбрать товар</th>
                <th width="8%">Кол-во</th>
                <th width="8%">Цена (вход ₴)</th>
                <th width="8%">Цена (вход $)</th>
                <th width="7%">Цена1 (₴)</th>
                <th width="7%">Цена2 (₴)</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr class="add-row-prod">
                <td>
                    <div class="form-group required" data-type="true">
                        <?=  
                            Html::dropDownList('category','',[],[
                                'class' => 'form-control product-category not-send',
                                'placeholder' => 'Выбрать категорию'
                            ]);
                        ?>
                    </div>
                </td>
                <td>
                    <div class="form-group choose-prod" data-type="true">
                        <?= $form->field($model, 'product_id')->dropDownList([],['data-page' => 1])->label(false); ?>
                        <div class="help-block"></div>
                    </div>
                </td>
                <td>
                    <?= $form->field($model, 'amount')->textInput(['type' => 'number','min' => 0, 'placeholder' => "Выбрать кол-во товара"])->label(false) ?>
                </td>
                <td>
                    <input type="float" id="ua-price" class="form-control not-send" min="0" name="OperComing[start_price_ua]" aria-required="true" placeholder="Выбрать цену">
                    <div class="help-block"></div>
                </td>
                <td>
                    <?= $form->field($model, 'start_price')->textInput(['type' => 'float','min' => 0, 'placeholder' => "Выбрать цену"])->label(false) ?>
                </td>
                <td>
                    <?= $form->field($model, 'price2')->textInput(['type' => 'float','min' => 0, 'placeholder' => "Выбрать цену"])->label(false) ?>
                </td>
                <td>
                    <?= $form->field($model, 'price1')->textInput(['type' => 'float','min' => 0, 'placeholder' => "Выбрать цену"])->label(false) ?>
                </td>
                <td style="display: none">
                    <?= $form->field($model, 'transaction_id')->textInput(['value' => $transaction_id])->label(false) ?>
                    <?= $form->field($model, 'product_id')->textInput([])->label(false) ?>
                </td>
            </tr>
            </tbody>
        </table>

        <div id="product_variant_table"></div>
        <?= Html::submitButton('Добавить', ['class' => 'add-prod snap']) ?>
    </div>
<?php ActiveForm::end();?>
