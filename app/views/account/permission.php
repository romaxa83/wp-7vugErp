<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Редактирование роли';
?>
<?php if($roles && $permissions) { ?>
    <div class="btn-group">
        <?= Html::a('Создать роль',['/permission/add-role'],['class' => 'btn btn-warning'])?>
        <?= Html::a('Создать разрешение',['/permission/add-permission'],['class' => 'btn btn-warning'])?>
    </div>
<div class="container">
    <div class="row">
        <div class="col-lg-6 col-xs-8">

        </div>
        <?php foreach ($roles as $role => $desc) { ?>
            <div class="col-lg-2 col-xs-4 text-center">
                <p><strong><?= $desc;?></strong></p>
            </div>
       <?php } ?> 
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <?php foreach ($permissions as $perm => $perm_desc) { ?>
        <div class="row">
            <div class="col-lg-6 col-xs-8">
                <p><strong><?= $perm_desc;?></strong></p>
            </div>
            <?php foreach ($roles as $role => $desc) { ?>
               <?php $role_permit = array_keys(Yii::$app->authManager->getPermissionsByRole($role)); ?>
                <div class="col-lg-2 col-xs-4 text-center">
                    <?php if(in_array($perm, $role_permit)){ ?>
                        <?= Html::checkbox($role.'[]', true, ['value' => $perm]);?>
                    <?php } else{ ?>
                        <?= Html::checkbox($role.'[]', false, ['value' => $perm]);?>
                    <?php } ?>
                </div>
            <?php } ?>
        </div><hr style="margin-top: 0;margin-bottom: 0">
    <?php } ?>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php } ?>