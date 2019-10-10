<?php

$this->title = 'Редактирования продукта:'.$model->name;
?>
<div class="content">
    <?php 
        echo $this->render('_form-product', [
            'model' => $model,
            'categories' => $categories,
            'agents' => $agents,
        ]);
    ?>
</div>
