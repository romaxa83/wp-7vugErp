<?php

use app\models\Agent;
use app\models\Operations;
use kartik\datetime\DateTimePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $agents */
/* @var $repository */
/* @var $model */
/* @var $categories */

$this->title = 'Приход товара';
?>

<div class="container-fluid">
    <div class="row">
        <?php if (empty(Yii::$app->request->get(1))):?>
        <div class="col-xs-12 col-sm-6">
            <div class="operation-coming-form">

                <?php $form=ActiveForm::begin(['action' => 'create'])?>
                    <?= $form->field($model, 'whence')->widget(Select2::classname(), [
                        'data' => $agents,
                        'language' => Yii::$app->language,
                        'options' => [
                            'placeholder' => 'Выберите поставщика',
                            'class' => 'form-control'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]); ?>

                    <?= $form->field($model,'where')->textInput()->dropDownList($repository)->label('Склад')?>

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

                    <?= Html::submitButton('Cоздать',['class' => 'snap start_add_prod','style' => 'border:0;','title' => 'Создаст приходную транзакцию. Убедитесь, что все поля заполненны'])?>

                <?php ActiveForm::end()?>

            </div>

        </div>

        <div class="col-sm-offset-1 col-sm-5 lost-transaction">
            <?php if (!empty($lost_transaction)): ?>
                <?= $this->render('../operation/lost-transaction', [
                    'lost_transaction' => $lost_transaction,
                    'agents' => $agents,
                    'shop' => false
                ]); ?>
            <?php endif;?>
        </div>
    </div>
    <?php endif;?>
</div>