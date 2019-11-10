<table id="transaction-table" class="table">
    <thead>
        <tr>
            <th rowspan="2" style="vertical-align: middle;padding: 1px;font-size: 12px;">№</th>
            <th rowspan="2" style="vertical-align: middle;padding: 1px;font-size: 12px;text-align: center">Продукт</th>
            <th rowspan="2" style="vertical-align: middle;padding: 1px;font-size: 12px;">Ед.изм.</th>
            <th rowspan="2" style="vertical-align: middle;padding: 1px;font-size: 12px;">Кол-во</th>
            <th style="padding: 1px;font-size: 12px;">Цена $</th>
            <th style="padding: 1px;font-size: 12px;">Цена ₴</th>
            <th style="padding: 1px;font-size: 12px;">Cумма $</th>
            <th style="padding: 1px;font-size: 12px;">Cумма ₴</th>
        </tr>
        <tr>
            <th colspan="2" style="text-align:center;font-size: 8px;padding: 1px">Оптовая цена</th>
            <th colspan="2" style="text-align:center;font-size: 8px;padding: 1px">Оптовая цена</th>
        </tr>
    </thead>
    <tbody>
        <?php 
            for ($i = 0; $i < count($product); $i++) : 
            $chars = $product[$i]->vproduct_id === NULL ? '' : $product[$i]->vproduct->chars;
        ?>
            <tr>
                <td style="padding: 0 0 0 5px;font-size: 12px;border: 1px solid rgb(200,202,203)"><?= $i + 1 ?></td>
                <td style="padding: 0 0 0 5px;font-size: 12px;border: 1px solid rgb(200,202,203)"><?= $product[$i]->product->name . $chars ?></td>
                <td style="padding: 0 0 0 5px;font-size: 12px;border: 1px solid rgb(200,202,203)"><?= $product[$i]->product->unit ?></td>
                <td style="padding: 0 0 0 5px;font-size: 12px;border: 1px solid rgb(200,202,203)"><?= $product[$i]->amount ?></td>
                <td style="padding: 0 0 0 5px;font-size: 12px;border: 1px solid rgb(200,202,203)"><?= formatedPriceUSD($product[$i]->trade_price); ?></td>
                <td style="padding: 0 0 0 5px;font-size: 12px;border: 1px solid rgb(200,202,203)"><?= formatedPriceUSD(getConvertUSDinUAH($product[$i]->trade_price, $product[$i]->transaction->course)); ?></td>
                <td style="padding: 0 0 0 5px;font-size: 12px;border: 1px solid rgb(200,202,203)"><?= formatedPriceUSD($product[$i]->trade_price * $product[$i]->amount); ?></td>
                <td style="padding: 0 0 0 5px;font-size: 12px;border: 1px solid rgb(200,202,203)"><?= formatedPriceUSD(getConvertUSDinUAH($product[$i]->trade_price * $product[$i]->amount, $product[$i]->transaction->course)); ?></td>
            </tr>
        <?php endfor;?>
    </tbody>
</table>