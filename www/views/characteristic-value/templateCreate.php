<?php

use yii\helpers\Url;
?>
<div class="form-group level-2">
    <div class="input-group">
        <div class="expand-plus form-control" aria-expanded="true">
            <input name="characteristic-name" style="width: 100%;" />
        </div>
        <div class="input-group-addon addon-transparent size3">
            <a href="<?= Url::toRoute('characteristic-value/create', true); ?>" type="button" class="btn no-btn create-characteristic"><i class="fa fa-plus"></i></a>
            <a href="<?= Url::toRoute('characteristic-value/remove', true); ?>" type="button" class="btn no-btn remove-characteristic"><i class="fa fa-trash-o"></i></a>
        </div>
    </div>
</div>