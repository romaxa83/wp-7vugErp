<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Agent */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(['action' => (Yii::$app->controller->id == 'agent' && Yii::$app->controller->action->id == 'update') ? '/agent/update?id='  . Yii::$app->request->get('id') : '/agent/create']); ?>
<div class="row">
    <div class="col-sm-6">
        <?= $form->field($model_agent, 'type')->textInput()->dropDownList([
            '1' => 'Поставщик',
            '2' => 'Магазин',
            '3' => 'Склад',
        ],
        ['prompt' => 'Выбрать тип','value' => 
            (Yii::$app->controller->action->id == 'store' && Yii::$app->controller->id != 'product' || Yii::$app->request->get('type') == 2) ? '2' : '1' ,
            'class' => (Yii::$app->controller->action->id == 'create' && Yii::$app->controller->id == 'agent') ? 'form-control' : 'form-control hidden'
        ]) ?>
        <?= $form->field($model_agent, 'firm')->textInput(['maxlength' => true]) ?>

        <?php if (Yii::$app->request->get('type') == 1):?>
            <?= $form->field($model_agent, 'name')->textInput(['maxlength' => true])->label('Представитель') ?>
        <?php endif;?>

        <?= $form->field($model_agent, 'address')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model_agent, 'telephone')->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '+38 (999) 999 99 99',
            ])->textInput() 
        ?>

    </div>
    <div class="col-sm-6">
        <div class="add_new_header row">
            <div class="col-sm-12">
                <div class="pull-right form-group field-agent-status">
                    <input id="agent-status" name="Agent[status]" type="checkbox" <?= $model_agent->status == 1 ? 'checked' : null; ?>>
                    <label for="agent-status" data-text-true="Вкл" data-text-false="Выкл"><i></i></label>
                </div>
            </div>
        </div>
        <?= $form->field($model_agent, 'data')->textarea(['rows' => 10]) ?>
        
        <?php if (Yii::$app->request->get('type') == 2):?>
            <?= $form->field($model_agent,'price_type')->dropDownList([
                '1' => 'Цена 1',
                '2' => 'Цена 2'
            ],['prompt' => 'Выбрать цену']) ?>
        <?php endif;?>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="new_prodact_buttons inline row">
            <div class="form-group">
                <?php 
                echo Html::submitButton($model_agent->isNewRecord ? 'Сохранить' : 'Редактировать', ['class' =>  (Yii::$app->controller->id == 'product') ? 'send-agent snap' : 'save-agent snap']);
                if (Yii::$app->controller->id == 'agent'){
                    if(Yii::$app->controller->action->id == 'create'){
                        echo Html::a('Отмена',['/product/index'],['class' => 'snap _gray']);
                    }elseif (Yii::$app->controller->action->id == 'update') {
                        echo Html::a('Отмена',['/agent/'.Yii::$app->request->get('type') === 1 ? 'provider' : 'store'],['class' => 'snap _gray']);
                    }
                }else{ 
                    echo Html::button('Отмена',['class' => 'hide-form snap _gray','data-form' => 'agent']);
                } ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
