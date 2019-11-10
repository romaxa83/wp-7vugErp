<?php 
    $index = 1;
    $changePrice = '<p>Внимание! Изменились цены на следующие позиции:</p>';
?>
<div>
    <h5 align="right">Магазин: <?= $shop[$operation['where']] ?></h5>
    <h4>Накладная на расход № <?= $operation['transaction'] ?> от <?= $operation['date'] ?></h4>
    <?php
        if($type === 'default'){
            echo $this->render('pdf-table-default',['product' => $product]);
        }
        if($type === 'cost_price'){
            echo $this->render('../pdf/pdf-table-cost_price',['product' => $product]);
        }
        if($type === 'trade_price'){
            echo $this->render('pdf-table-trade_price',['product' => $product]);
        }
        if($type === 'price'){
            echo $this->render('pdf-table-price',['product' => $product]);
        }
    ?>
    <hr style="margin-top: -10px">
    <div>
        <p align="right">Итого:  <?= formatedPriceUA($operation->total_ua)?>(UAH)</p>
        <p>Всего товаров <?= count($product) ?> на общую сумму <?= formatedPriceUA($operation->total_ua) ?></p>
        <p>Принял:</p>
    </div>
    <?= ($index != 1) ? '<hr>' . $changePrice : '' ?>
</div>