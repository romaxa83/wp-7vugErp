<?php

use yii\helpers\Url;
?>
<a href="<?= Url::toRoute('characteristic-value/update?id=' . $id, true); ?>" type="button" class="btn no-btn update-characteristic"><i class="fa fa-pencil"></i></a>
<a href="<?= Url::toRoute('characteristic-value/delete?id=' . $id, true); ?>" type="button" class="btn no-btn remove-characteristic"><i class="fa fa-trash-o"></i></a>
