<?php

$this->title = 'Добавления Контрагента';
?>
<div class="content">
    <?php 
        echo $this->render('_form-agent', [
            'model_agent' => $model
        ]);
    ?>
</div>
