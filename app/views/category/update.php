<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Category */
/* @var $parent_cats */
/* @var  $selectedChars */
/* @var  $chars */

$this->title = 'Редактировать категорию: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Категории', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="category-update content">
    <div id="category-form">
        <?= $this->render('_form-category', [
            'model_cat' => $model,
            'parent_cats' => $parent_cats,
            'chars_cat' => $chars
        ]) ?>
    </div>
</div>
