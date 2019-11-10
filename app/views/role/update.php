<?php
namespace app\views\permission;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Редактирование роли: ' . ' ' . $role->name;
$this->params['breadcrumbs'][] = ['label' => 'Управление ролями', 'url' => ['/account/index']];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="news-index">

    <div class="links-form col-lg-3">

        <?php $form = ActiveForm::begin(); ?>

        <div class="form-group">
            <?= Html::label('Название роли'); ?>
            <?= Html::textInput('name', $role->name, ['class' => 'form-control']); ?>
        </div>

        <div class="form-group">
            <?= Html::label('Текстовое описание'); ?>
            <?= Html::textInput('description', $role->description, ['class' => 'form-control']); ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Отмена',Url::toRoute('user/index'),['class' => 'btn btn-default'])?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
