<?php 
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
$form = ActiveForm::begin(['id'=>'form-where-whence','action'=>'/operation-consumption/update?id='.Yii::$app->request->get('id')]);?>
    <div class="col-xs-6">
        <?= 
            $form->field($model,'whence')->textInput()->widget(Select2::classname(), [
                'data' => $repository,
                'language' => Yii::$app->language,
                'options' => [
                    'class' => 'form-control'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Склад')
        ?>
    </div>
    <div class="col-xs-6">
        <?=
            $form->field($model, 'where')->widget(Select2::classname(), [
                'data' => $shop,
                'language' => Yii::$app->language,
                'options' => [
                    'placeholder' => 'Магазин выключен',
                    'class' => 'form-control',
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Магазин');
        ?>
    </div>
<?php ActiveForm::end();?>