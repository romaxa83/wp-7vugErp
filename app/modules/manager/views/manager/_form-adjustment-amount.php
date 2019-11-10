<?php 
use yii\widgets\ActiveForm;
?>
<?php 
    $form = ActiveForm::begin([
        'id' => 'adjustment-amount-product',
        'action' => '/manager/manager/adjustment-amount',
        'options' => [
            'class' => 'form-horizontal'
        ],
        'fieldConfig' => [
            'options' => [
                'class' => ''
            ]
        ],
    ]);
?>
<div class="row">
    <div class="col-xs-12 col-md-6"> 
        <div class="row">
            <div class="col-xs-12">
                <?= 
                    $form->field($model, 'name')->textInput(['readonly' => true])->label(false);
                ?>
                <?= 
                    $form->field($model, 'request_id')->hiddenInput(['readonly' => true])->label(false);
                ?>
                <?= 
                    $form->field($model, 'product_id')->hiddenInput(['readonly' => true])->label(false);
                ?>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-6"> 
        <div class="row">
            <div class="col-xs-6">
                <?= 
                    $form->field($model, 'amount')->textInput(['type' => 'number'])->label(false);
                ?>
            </div>
            <div class="col-xs-6">
                <div class="row">
                    <div class="col-xs-6">
                        <button type="submit" class="snap left-15 add">Добавить</button>
                    </div>
                    <div class="col-xs-6">
                        <button type="button" class="snap left-15 back">Назад</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
