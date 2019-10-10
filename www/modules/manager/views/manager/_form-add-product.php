<?php 
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>
<?php 
    $form = ActiveForm::begin([
        'id' => 'add-request-product-form',
        'action' => '/manager/manager/add-product',
        'options' => [
            'class' => 'form-horizontal'
        ],
        'fieldConfig' => [
            'options' => [
                'class' => ''
            ]
        ],
        'enableClientValidation' => false
    ]);
?>
<div class="row">
    <div class="col-xs-12 col-md-6"> 
        <div class="row">
            <div class="col-xs-12 category-field">
                <?=   
                    Html::dropDownList('category','',[],[
                        'id' => 'category_filter',
                        'class' => 'form-control not-send',
                        'placeholder' => 'Выбрать категорию',
                    ]);
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 product-field">
                <?= 
                    $form->field($model,'product_id')->dropDownList([],[
                        'id' => 'product_filter',
                        'class' => 'form-control',
                        'placeholder' => 'Выбрать товар'
                    ])->label(false);
                ?>
                <?= 
                    $form->field($model,'request_id')->hiddenInput([
                        'value' => $request->id
                    ])->label(false);
                ?>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-6"> 
        <div class="row">
            <div class="col-xs-6">
                <?= 
                    $form->field($model,'amount')->textInput(['type' => 'number','min' => 0],[
                        'class' => 'form-control'
                    ])->label(false);
                ?>
            </div>
            <div class="col-xs-6">
                <button type="submit" class="snap left-15 add-product">Добавить</button>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>