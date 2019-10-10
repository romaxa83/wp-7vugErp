<?php 
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
?>
<?php $form = ActiveForm::begin(['id'=>'form-where-whence','action'=>'/operation-coming/update?id='.$model->id]);?>

    <div class="col-xs-6">
        <?=
        $form->field($model, 'whence')->widget(Select2::classname(), [
            'data' => $agents,
            'language' => Yii::$app->language,
            'options' => [
                'placeholder' => 'Поставщик выключен',
                'class' => 'form-control',
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model,'where')->textInput()->dropDownList($repository)->label('Склад')?>
    </div>

<?php ActiveForm::end();?>