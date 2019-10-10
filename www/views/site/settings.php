<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\UserExt */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Настройки';
$this->params['breadcrumbs'][] = $this->title;
?>

<section class="content">
    <div class="news-index">
        <div class="row">
            <div class="col-xs-12">
                <ul class="nav nav-tabs custom-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#tab1" aria-controls="tab1" role="tab" data-toggle="tab">Основные</a>
                    </li>
                    <li role="presentation">
                        <a href="#tab2" aria-controls="tab2" role="tab" data-toggle="tab">Реквизиты предприятия</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="tab1">
                        <div class="row next-row">
                            <div class="col-xs-12 col-sm-6 col-lg-6">
                                <?php $form = ActiveForm::begin(); ?>
                                <div class="">
                                    <?= $form->field($model,'usd')->textInput(['type' => 'float'])->label('Курс доллара',['class' => 'label-size']) ?>
                                </div>
                                <div class="">
                                    <?= $form->field($model,'per_trade_price')->textInput(['type' => 'float'])->label('Процент оптовой цены',['class' => 'label-size']) ?>
                                </div>
                                <div class="">
                                    <?= $form->field($model,'cat')->dropDownList([
                                            '10' => '10',
                                            '20' => '20',
                                            '50' => '50',
                                            '100' => '100'
                                    ])->label('Кол-во строк в списке категорий') ?>
                                </div>
                                <div class="">
                                    <?= $form->field($model,'prod')->dropDownList([
                                        '10' => '10',
                                        '20' => '20',
                                        '50' => '50',
                                        '100' => '100'
                                    ])->label('Кол-во строк в списке товаров') ?>
                                </div>
                                <div class="">
                                    <?= $form->field($model,'operation')->dropDownList([
                                        '10' => '10',
                                        '20' => '20',
                                        '50' => '50',
                                        '100' => '100'
                                    ])->label('Кол-во строк в списке приход/расход') ?>
                                </div>
                                <div class="">
                                    <?= $form->field($model,'store')->dropDownList([
                                        '10' => '10',
                                        '20' => '20',
                                        '50' => '50',
                                        '100' => '100'
                                    ])->label('Кол-во строк в списке магазинов') ?>
                                </div>
                                <div class="">
                                    <?= $form->field($model,'user')->dropDownList([
                                        '10' => '10',
                                        '20' => '20',
                                        '50' => '50',
                                        '100' => '100'
                                    ])->label('Кол-во строк в списке пользователей') ?>
                                </div>

                                <div class="">
                                    <?= $form->field($model,'mes_change_price')->textInput(['type' => 'number','min' => 0])
                                        ->label('Кол-во сообщений в накладной при изменении розничной цены') ?>
                                </div>
                                <div>
                                    <?= $form->field($model,'float_ua')->textInput(['type' => 'number','min' => 0,'max' => 10])
                                        ->label('Кол-во знаков после запятой для цены в гривнах')?>
                                </div>
                                <div>
                                    <?= $form->field($model,'float_usd')->textInput(['type' => 'number','min' => 0,'max' => 10])
                                        ->label('Кол-во знаков после запятой для цены в долларах')?>
                                </div>

                                <div class="form-group">
                                    <div class="w-100 text-left new_prodact_buttons inline row">
                                        <?= Html::submitButton('Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success snap col-sm-4' : 'btn btn-success snap col-sm-4']) ?>
                                        <?= Html::a('Отмена',Url::toRoute(Yii::$app->user->getReturnUrl()),['class' => 'btn btn-default snap col-sm-4'])?>
                                    </div>
                                </div>
                                <?php ActiveForm::end(); ?>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab2">
                        <div class="row next-row">
                            <div class="col-xs-12 col-sm-9 col-lg-6">
                                <?php $form = ActiveForm::begin(); ?>
                                <div class="">
                                    <?= $form->field($model,'boss')->textInput()->label('Директор',['class' => 'label-size']) ?>
                                </div>
                                <div class="">
                                    <?= $form->field($model,'name_firm')->textInput()->label('Название',['class' => 'label-size']) ?>
                                </div>
                                <div class="">
                                    <?= $form->field($model,'address')->textarea(['rows' => '6'])->label('Адрес',['class' => 'label-size']) ?>
                                </div>
                                <div class="">
                                    <?= $form->field($model,'property')->textarea(['rows' => '6'])->label('Реквизиты',['class' => 'label-size']) ?>
                                </div>
                                <div class="form-group">
                                    <div class="w-100 text-left new_prodact_buttons inline row">
                                        <?= Html::submitButton('Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success snap col-sm-4' : 'btn btn-success snap col-sm-4']) ?>
                                        <?= Html::a('Отмена',Url::toRoute(Yii::$app->user->getReturnUrl()),['class' => 'btn btn-default snap col-sm-4'])?>
<!--                                        --><?php //debug(Yii::$app->user->getReturnUrl());?>
                                    </div>
                                </div>
                                <?php ActiveForm::end(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
































