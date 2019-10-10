<?php

use yii\helpers\Html;
use app\modules\news\Module;

/* @var $this yii\web\View */
/* @var $model app\models\UserExt */
/* @var $stores */

$this->title = 'Редактировать пользователя';
// $this->params['breadcrumbs'][] = ['label' => Module::t('module', 'NEWS_ARTICLE'), 'url' => ['index']];
// $this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-category-update">

    <?= $this->render('_form', [
        'model' => $model,
        'stores' => $stores,
    ]) ?>

</div>
