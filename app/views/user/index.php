<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

use yii\widgets\Pjax;

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

        <?php
        if (!empty($error)) {
            ?>
            <div class="error-summary">
                <?php
                    echo implode('<br>', $error);
                ?>
            </div>
            <?php
        }
        ?>
        
        <div id="role-form" class="row hidden">
            <?php $form = ActiveForm::begin([
                'action' => '/role/create',
            ]); ?>
            <div class="col-xs-12 col-sm-6 col-lg-6">

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
                        <?= Html::a('Отменить',Url::toRoute('user/index'),['class' => 'btn btn-default ml-1','type' => 'reset']) ?>
                    </div>
                </div>

            </div>
            <?php ActiveForm::end(); ?>
        </div>
        
        <div id="user-form" class="row hidden">
            <div class="col-xs-12 col-sm-12 col-lg-12">
                <?php $form = ActiveForm::begin(['action' => '/user/create',]); ?>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-inline">
                            <?= $form->field($model, 'username')->textInput()->label('Логин',['class' => 'label-size']) ?>
                        </div>
                        <div class="form-inline">
                            <?= $form->field($model, 'email')->textInput()->label('Email',['class' => 'label-size']) ?>
                        </div>
                        <div class="form-inline">
                            <?= $form->field($model, 'password')->textInput()->label('Пароль',['class' => 'label-size']) ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-inline">
                            <?= $form->field($model, 'role')->dropDownList(ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'description'),[
                                'prompt' => 'Выберите роль','class' => 'store-sel custom-select'
                            ])->label('Роль',['class' => 'label-size']) ?>
                        </div>
                        <div class="form-inline  store">
                            <?= $form->field($model,'store_id')->dropDownList($stores,[
                                'prompt' => 'Выберите магазин','class' => 'custom-select'
                            ])->label('Магазин',['class' => 'label-size']) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="new_prodact_buttons inline row">
                            <div class="form-group">
                                <?= Html::submitButton('Сохранить', ['class' => $model->isNewRecord ? 'save-role btn btn-success' : 'btn btn-primary']) ?>
                                <?= Html::a('Отменить',Url::toRoute('user/index'),['class' => 'btn btn-default ml-1','type' => 'reset']) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>

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
                                                <a href="/user/update?id=<?= $user['id']?>" type="button" class="btn no-btn" title="Редактировать пользователя <?=$user['username']?>">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                            </td>
                                            <td class="not-in">
                                                <?php if($user['role'] !== 'admin') : ?>
                                                <a href="/user/delete?id=<?= $user['id']?>" type="button" class="btn no-btn" title="Удалить пользователя <?=$user['username']?>">
                                                    <i class="fa fa-trash-o"></i>
                                                </a>
                                                <?php endif; ?> 
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
                                <table class="custom-table">
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
                                    'action' => '/role/permission',
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
                                            <?= Html::a('Отменить',Url::toRoute('user/index'),['class' => 'btn btn-default ml-1'])?>
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
                                            <?php if($role !== 'admin') : ?> 
                                            <td class="not-in">
                                                <a href="/role/update?name=<?= $role?>" type="button" class="btn no-btn" title="Редактировать роль <?= $role?>">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                            </td>
                                            <td class="not-in">
                                                <a href="/role/delete?name=<?= $role?>" type="button" class="btn no-btn" title="Удалить роль <?=$role?>">
                                                    <i class="fa fa-trash-o"></i>
                                                </a>
                                            </td>
                                            <?php endif; ?> 
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


