<?php

$this->title = 'Редактирования Контрагента:'.$model->firm;
?>
<div class="content">
    <?php 
        echo $this->render('_form-agent', [
            'model_agent' => $model
        ]);
    ?>
</div>