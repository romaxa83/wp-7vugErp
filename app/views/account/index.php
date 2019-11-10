<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $users */
/* @var $stores */
/* @var $model */
/* @var $roles */
/* @var $permissions */
/* @var $role_model */

$this->title = 'Пользователи';
?>
<section class="content">
    <div class="news-index">
        <div class="row">
            <div class="col-xs-12 col-sm-12 text-right control-btn">
                <?= Html::button('Добавить роль',['class' => 'show-form snap','data-form' => 'role', 'data-fixed' => 'false', 'title' => 'Появится форма добавления роли'])?>
                <?= Html::button('Добавить пользователя',['class' => 'show-form snap','data-form' => 'user', 'data-fixed' => 'false', 'title' => 'Появится форма добавления пользователя'])?>
                <?= Html::button('Отменить',['class' => 'hide-form snap _gray','title' => 'Убрать форму'])?>
            </div>
        </div>      
        <div class="row role-form add-form">
            <div class="col-xs-12 col-sm-6 col-lg-6">
                <?php $form = ActiveForm::begin([
                    'action' => '/permission/add-role',
                ]); ?>
                <p class="error hidden" style="color:red;font-size:16px;margin-left:105px">Будьте любезны ввести роль латиницей</p>
                <p class="exist-role hidden"></p>
                <div class="form-inline">
                    <?= $form->field($role_model, 'name')->textInput(['placeholder'=>'Роль обязательно указывать латиницей'])->label('Роль',['class' => 'label-size']) ?>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6 col-lg-6">
                <div class="form-inline">
                    <?= $form->field($role_model, 'description')->textInput()->label('Описание',['class' => 'label-size']) ?>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6 col-lg-6">
                <div class="form-group">
                    <div class="input-group w-100 text-left">
                        <?= Html::submitButton('Сохранить', ['class' => $role_model->isNewRecord ? 'save-role btn btn-success' : 'btn btn-primary']) ?>
                        <?= Html::a('Отменить',Url::toRoute('account/index'),['class' => 'btn btn-default ml-1','type' => 'reset']) ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
            <?= $this->render('_form',['model'=>$model,'stores'=>$stores]); ?>
        <div class="row">
            <div class="col-xs-12">
                <ul class="nav nav-tabs custom-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#tab1" aria-controls="tab1" role="tab" data-toggle="tab">Пользователи</a>
                    </li>
                    <li role="presentation">
                        <a href="#tab2" aria-controls="tab2" role="tab" data-toggle="tab">Матрица ролей</a>
                    </li>
                    <li role="presentation">
                        <a href="#tab3" aria-controls="tab3" role="tab" data-toggle="tab">Роли</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="tab1">
                        <div class="row">
                            <div class="col-xs-12">
                                <table class="custom-table">
                                    <thead>
                                    <tr>
                                        <th class="top-left">Логин</th>
                                        <th>E-mail</th>
                                        <th>Роль</th>
                                        <th class="top-right">Пароль</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                        <?php foreach($users as $user):?>
                                        <tr>
                                            <td><?= $user['username']?></td>
                                            <td><?= $user['email']?></td>
                                            <td><?= $user['role']?></td>
                                            <td><?= $user['password']?></td>
                                            <td class="not-in">
                                                <a href="/account/update?id=<?= $user['id']?>" type="button" class="btn no-btn" title="Редактировать пользователя <?=$user['username']?>">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                            </td>
                                            <td class="not-in">
                                                <a href="/account/delete?id=<?= $user['id']?>" type="button" class="btn no-btn" title="Удалить пользователя <?=$user['username']?>">
                                                    <i class="fa fa-trash-o"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach;?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab2">
                        <div class="row new-row">
                            <div class="col-xs-12">
                                <table class="custom-table v2 main">
                                    <thead>
                                    <tr>
                                        <th class="td-title">Разрешения:</th>
                                        <?php foreach ($roles as $role):?>
                                            <th class="td-option"><?= $role?></th>
                                        <?php endforeach;?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $form = ActiveForm::begin([
                                    'action' => '/account/permission',
                                    ]); ?>
                                    <?php foreach ($permissions as $perm => $desc_perm ):?>
                                        <tr>
                                        <td><?=$desc_perm?></td>
                                            <?php foreach ($roles as $role => $desc):?>
                                                <?php $role_permit = array_keys(Yii::$app->authManager->getPermissionsByRole($role)); ?>

                                                <td class="td-option">
                                                    <div class="form-group form-check">
                                                        <div class="input-group input-check">
                                                <?php if(in_array($perm, $role_permit)): ?>
                                                    <?= Html::checkbox($role.'[]', true, ['value' => $perm,'class' => 'custom-checkbox']);?>
                                                <?php else :?>
                                                    <?= Html::checkbox($role.'[]', false, ['value' => $perm, 'class' => 'custom-checkbox']);?>
                                                <?php endif;?>
                                                        </div>
                                                    </div>
                                                    <hr style="margin-top: 0;margin-bottom: 0">
                                                </td>
                                            <?php endforeach;?>
                                        </tr>
                                    <?php endforeach;?>
                                    </tbody>
                                </table>
                                <div class="col-xs-12 text-right">
                                    <div class="form-group">
                                        <div class="input-group w-100 text-left">
                                            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                                            <?= Html::a('Отменить',Url::toRoute('account/index'),['class' => 'btn btn-default ml-1'])?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php ActiveForm::end()?>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab3">
                        <div class="row">
                            <div class="col-xs-4">
                                <table class="custom-table">
                                    <thead>
                                    <tr>
                                        <th class="top-left">Роль</th>
                                        <th class="top-right">Описание</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <?php foreach($roles as $role => $desc):?>
                                        <tr>
                                            <td><?= $role?></td>
                                            <td><?= $desc?></td>
                                            <td class="not-in">
                                                <a href="/permission/update-role?name=<?= $role?>" type="button" class="btn no-btn" title="Редактировать роль <?= $role?>">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                            </td>
                                            <td class="not-in">
                                                <a href="/permission/delete-role?name=<?= $role?>" type="button" class="btn no-btn" title="Удалить роль <?=$role?>">
                                                    <i class="fa fa-trash-o"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach;?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>