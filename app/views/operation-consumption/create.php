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

$this->title = 'Расход товара';
?>

<div class="container-fluid">
    <div class="row">
        <?php if (empty(Yii::$app->request->get(1))):?>
        <div class="col-xs-12 col-sm-6">
            <div class="operation-coming-form">
                <?= Html::label('Создание массовой транзакции','check-mass')?>
                <?= Html::checkbox('mass','',['id' => 'check-mass','class' => 'choose_mass_transaction'])?>
                <?php $form=ActiveForm::begin(['action' => 'create'])?>
                    <?= $form->field($model, 'whence')->textInput()->widget(Select2::classname(), [
                        'data' => $repository,
                        'language' => Yii::$app->language,
                        'options' => [
                            'class' => 'form-control'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]); ?>
                    <?= $form->field($model, 'where')->widget(Select2::classname(), [
                        'data' => $shop,
                        'language' => Yii::$app->language,
                        'options' => [
                            'placeholder' => 'Выберите магазин',
                            'class' => 'form-control'
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

                    <?= Html::submitButton('Cоздать',['class' => 'snap start_add_prod','style' => 'border:0;','title' => 'Создаст приходную транзакцию. Убедитесь, что все поля заполненны'])?>

                <?php ActiveForm::end()?>

            </div>

        </div>

        <div class="col-sm-offset-1 col-sm-5 lost-transaction">
            <?php if (!empty($lost_transaction)):?>
                <?= $this->render('../operation/lost-transaction', [
                    'lost_transaction' => $lost_transaction,
                    'shop' => $shop,
                    'agents' => false
                ]); ?>
            <?php endif;?>
        </div>
    </div>
    <?php endif;?>
</div>