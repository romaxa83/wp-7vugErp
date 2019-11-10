<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

$controllers = [];
$action = [];
$index = 0;
foreach ($data as $k => $v) {
    $i = strtolower(str_replace('Controller', '', $k));
    $controllers[$i] = $k;
    foreach ($v as $k1 => $v1) {
        if ($i == $model->controller || $index == 0){
            $action[$v1] = $v1;
        }
    }
    $index++;
}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-5">
            <div class="user-form">
                <?php $form = ActiveForm::begin(); ?>
                <?php echo $form->field($model, 'name')->textInput() ?>
                <?php echo $form->field($model, 'controller')->dropDownList($controllers); ?>
                <?php echo $form->field($model, 'action')->dropDownList($action); ?>
                <?php echo $form->field($model, 'status')->dropDownList([1 => 'Закрыть доступ', 0 => 'Открыть доступ']); ?>
                <div class="form-group">
                    <?php echo Html::submitButton('Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
                    <?php echo Html::a('Вернуться', ['/access'], ['class' => 'btn btn-default']); ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>