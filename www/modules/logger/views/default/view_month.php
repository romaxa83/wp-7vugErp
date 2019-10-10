<div class="box-group" id="accordion_month">
<!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
<?php foreach($month as $one){ ?>
    <div class="panel box box-success">
        <div class="box-header with-border">
            <h4 class="box-title">
                <a data-toggle="collapse" data-parent="#accordion_month" href="#collapse<?= $one ?>" aria-expanded="true" class="get-more" data-type="day" data-month="<?= $one ?>" data-year="<?= $year ?>">
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