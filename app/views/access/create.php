<?php

use yii\helpers\Html;
use app\modules\news\Module;

$this->title = 'Создать правило';
?>
<div class="news-category-create">
    <?php
    echo $this->render('_form', [
        'data' => $data,
        'model' => $model
    ])
    ?>
</div>
