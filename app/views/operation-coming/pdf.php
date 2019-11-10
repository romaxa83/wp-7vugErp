<div>
    <h5 align="right">Поставщик: <?= $agents[$operation['whence']] ?></h5>
    <h4>Накладная на приход № <?= $operation['transaction'] ?> от <?= $operation['date'] ?></h4>
    <?php
        if($type === 'default'){
            echo $this->render('pdf-table-default',['product' => $product]);
        }
        if($type === 'cost_price'){
            echo $this->render('../pdf/pdf-table-cost_price',['product' => $product]);
        }
        if($type === 'start_price'){
            echo $this->render('pdf-table-start_price',['product' => $product]);
        }
    ?>
    <hr>
    <div>
        <p align="right">Итого: <?= formatedPriceUSD($operation->total_usd) ?>(USD)</p>
        <p align="right">Итого:  <?= formatedPriceUA($operation->total_ua) ?>(UAH)</p>
        <p>Всего товаров <?= count($product) ?> на общую сумму <?= formatedPriceUSD($operation->total_usd) ?></p>
        <p>Принял:</p>
    </div>
</div>