<?php

/* @var $transaction  /OperationController/createMassTransaction */
/* @var $model  /OperationController/createMassTransaction */
/* @var $store  /OperationController/createMassTransaction */

use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

$this->title = 'Массовая расходная транзакция';
$this->params['breadcrumbs'][] = ['label' => 'Перемещение товара', 'url' => ['/operations/index']];
$this->params['breadcrumbs'][] = ['label' => 'Список pacхода товара', 'url' => ['/operations/consumption-operations']];
?>

<div class="content">
    <div class="row">
        <div class="col-xs-5">
            <div class="operation-consumption-form">

                <?php $form = ActiveForm::begin()?>

                <?= $form->field($model, 'whence')->textInput()->widget(Select2::classname(), [
                    'data' => $repository,
                    'language' => Yii::$app->language,
                    'options' => [
                        'class' => 'form-control',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>
                
                <?= $form->field($model, 'where')->textInput()->widget(Select2::classname(), [
                    'data' => $shop,
                    'language' => Yii::$app->language,
                    'options' => [
                        'class' => 'form-control',
                        'multiple' => true,
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>
                
                <?= 
                    $form->field($model, 'date')->textInput(['maxlength' => true])
                        ->widget(DateTimePicker::className(), [
                            'language' => 'ru',
                            'options' => ['autocomplete' => 'off', 'readonly' => 'readonly'],
                            'pluginOptions' => [
                                'minuteStep' => 1,
                                'format' => 'yyyy-mm-dd hh:ii:ss',
                                'autoclose' => true,
                            ]
                        ]);
                ?>
                <?= Html::submitButton('Cоздать',['class' => 'snap','title' => 'Создаст расходную транзакцию. Убедитесь, что все поля заполнены'])?>
                <?php ActiveForm::end()?>
            </div>
        </div>
        <div class="col-xs-2 added-store">
            <h4>Выбранные магазины:</h4>
            <div class="stores"></div>
        </div>
    </div>
</div>