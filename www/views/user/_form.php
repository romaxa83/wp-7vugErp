<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\UserExt */
/* @var $form yii\widgets\ActiveForm */
/* @var $stores */

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-5">
            <div class="user-form">
                <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'username')->textInput() ?>

                <?= $form->field($model, 'password')->textInput() ?>

                <?= $form->field($model, 'email')->textInput() ?>

                <?= $form->field($model, 'role')->dropDownList(ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'description'),[
                    'prompt' => 'Выберите роль'
                ]) ?>

                <?= $form->field($model,'store_id')->dropDownList($stores,[
                    'prompt' => 'Выберите магазин',
                ]) ?>

                <div class="form-group">
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                    <?= Html::a('Вернуться', ['/user/index'],['class' => 'btn btn-default']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>
