<?php

/**
 * @var $this yii\web\View
 * @var $model lav45\activityLogger\modules\models\ActivityLogSearch
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;

if (isset(Yii::$app->params['datePicker-language'])) {
    $language = Yii::$app->params['datePicker-language'];
} else {
    $language = substr(Yii::$app->language, 0, 2);
}
?>
<style>
    #activity_log .form-group.field-date_from,
    #activity_log .form-group.field-date_to {
        display: inline;
    }
    #activity_log ul {
        margin-top: 0;
        margin-bottom: 0;
        /*padding-left: 10px;*/
    }
</style>
<div class="row ">
    <?php
    $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['index'],
                'layout' => 'inline',
    ]);
    ?>
    <div class="col-md-2">
        <?php echo $form->field($model, 'userId')->dropDownList($model->getUserList(), ['prompt' => 'Все пользователи', 'style' => 'width: 100%;']); ?>
    </div>
    <div class="col-md-2">
        <?php echo $form->field($model, 'entityName')->dropDownList($model->getEntityNameList(), ['prompt' => 'Все модели', 'style' => 'width: 100%;']); ?>
    </div>
    <div class="col-md-2">
        <?php echo $form->field($model, 'action')->dropDownList($model->getActionList(), ['prompt' => 'Все действия', 'style' => 'width: 100%;']); ?>
    </div>
    <div class="col-md-3 text-left inline">
        <?=
        $form->field($model, 'date_from')->label(false)->widget(DatePicker::class, [
            'name' => $language,
            'language' => 'ru',
            'type' => DatePicker::TYPE_INPUT,
            'options' => ['placeholder' => 'От', 'style' => 'margin-right:10px;width: 175px'],
            'convertFormat' => true,
            'pluginOptions' => [
                'format' => 'dd.MM.yyyy',
                'autoclose' => true,
                'weekStart' => 1, //неделя начинается с понедельника
                'todayBtn' => true, //снизу кнопка "сегодня"
            ]
        ]);
        ?>-
        <?=
        $form->field($model, 'date_to')->label(false)->widget(DatePicker::class, [
            'name' => $language,
            'language' => 'ru',
            'type' => DatePicker::TYPE_INPUT,
            'options' => ['placeholder' => 'До', 'style' => 'margin-left:10px;width: 175px'],
            'convertFormat' => true,
            'pluginOptions' => [
                'format' => 'dd.MM.yyyy',
                'autoclose' => true,
                'weekStart' => 1, //неделя начинается с понедельника
                'todayBtn' => true, //снизу кнопка "сегодня"
            ]
        ]);
        ?>
    </div>
    <div class="col-md-3 text-right">
        <?php echo Html::submitButton(Yii::t('lav45/logger', 'Search'), ['class' => 'snap']); ?>
        <?php echo Html::a(Yii::t('lav45/logger', 'Reset'), ['index'], ['class' => 'btn btn-default']); ?>
        <?php echo Html::a('<i class="fa fa-archive"></i>', ['out-put'], ['class' => 'snap', 'title' => 'Просмотреть архив']); ?>
        <a class="snap log-archive" data-count="<?= $countPage ?>" title="Заархивировать логи">
            <i class="fa fa-file-archive-o"></i>
        </a>
    </div>
    <?php ActiveForm::end(); ?>
</div>