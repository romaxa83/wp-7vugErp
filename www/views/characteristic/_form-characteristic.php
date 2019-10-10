<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<?php $form = ActiveForm::begin([
    'id' => 'characteristic-form',
    'action' => '/characteristic/create',
]);?>

    <div class="row">
        <div class="col-xs-8">
            <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
        </div>
    </div>

    <div class="input-group w-100 text-left">
        <?= Html::submitButton('Сохранить',['class' => 'send snap send-characteristic', 'from' => 'characteristic-form'])?>
        <?= Html::Button('Отменить',['class' => 'hide-form snap _gray', 'data-form' => 'characteristic'])?>
    </div>

<?php ActiveForm::end();?>