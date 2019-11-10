<?php

$this->title = 'Редактировать пользователя';
?>
<div class="container">
    <?= $this->render('_form', [
        'model' => $model,
        'stores' => $stores,
    ]) ?>
</div>
