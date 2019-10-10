<?php 
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<?php $form = ActiveForm::begin([
    'options' => ['enctype'=>'multipart/form-data'], 
    'action' => Url::toRoute('product/upload'), 
    'method' => 'POST'
]); ?>
<div class="input-file"><div class="img-input">Выбрать файл <i class="fa fa-upload"></i></div>
   <?php echo $form->field($model_csv,'file')->fileInput(['class' => 'snap inline'])->label('') ?>
</div>
<div class="form-group">
    <?php echo Html::submitButton('Загрузить',['class'=>'snap']) ?>
    <?= Html::button('Отмена',['class' => 'hide-import snap _gray', 'title' => 'Отменить импорт товаров'])?>
</div>
<?php ActiveForm::end(); ?>