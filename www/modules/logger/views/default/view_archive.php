<?php 
use yii\helpers\Url;
$this->title = 'Архив логов';
?>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <a href="<?= Url::to('index') ?>" class="btn btn-success back-to-activiti-log">Вернуться к журналу</a>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box-group" id="accordion-year">
                <?php foreach ($year as $one) { ?>
                    <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                    <div class="panel box box-success">
                        <div class="box-header with-border">
                            <h4 class="box-title">
                                <a data-toggle="collapse" data-parent="#accordion-year" href="#collapse<?= $one ?>" aria-expanded="true" class="get-more" data-type="month" data-year="<?= $one ?>">
                                    <?= $one ?>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse<?= $one ?>" class="panel-collapse collapse" aria-expanded="true" style="">
                            <div class="box-body">         

                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>