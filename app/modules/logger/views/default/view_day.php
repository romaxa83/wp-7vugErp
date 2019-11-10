<?php 
use yii\helpers\Url;
?>
<ul style="list-style: none;">
<?php foreach ($day as $one){ ?>
    <li>
        <a href="<?= Url::to('/uploads/log-archive/'.$year.'/'.$month.'/'.$one) ?>" download>
            <?= $one ?>
        </a>
    </li>    
<?php    } ?>
</ul>