<table id="transaction-table" class="table">
    <thead style="background: lightblue;font-style: italic">
        <tr style="border: none;">
            <th rowspan="2" style="padding:0 0 0 5px;vertical-align:top;width:3%;font-size:10px;">№</th>
            <th rowspan="2" style="padding:0 0 0 5px;vertical-align:top;width:60%;font-size:10px;">Продукт</th>
            <th style="padding:0 0 0 5px;vertical-align:top;width:5%;font-size:10px;">Ед.</th>
            <th rowspan="2" style="padding:0 0 0 5px;vertical-align:top;width:10%;font-size:10px;">Кол</th>
            <th rowspan="2" style="padding:0 0 0 5px;vertical-align:top;width:10%;font-size:10px;">Цена (₴)</th>
            <th rowspan="2" style="padding:0 0 0 5px;vertical-align:top;width:12%;font-size:10px;">Сумма (₴)</th>
        </tr>
        <tr style="border: none;">
            <th style="padding:0 0 0 5px;vertical-align:middle;width:5%;font-size:8px;">изм.</th>
        </tr>
    </thead>
    <tbody>
        <?php 
            for ($i = 0; $i < count($product); $i++) : 
            $chars = '';
// $chars = $product[$i]->vproduct_id === NULL ? '' : $product[$i]->vproduct->chars;
        ?>
            <tr>
                <td style="padding: 0;text-align:center;font-size: 12px;border: 1px solid rgb(200,202,203);"><?= $i + 1 ?></td>
                <td style="padding: 0 2px 0 2px;font-size: 12px;border: 1px solid rgb(200,202,203)"><?= $product[$i]->product->name . $chars ?></td>
                <td style="padding: 0 2px 0 2px;text-align:center;font-size: 12px;border: 1px solid rgb(200,202,203)"><?= $product[$i]->product->unit ?></td>
                <td style="padding: 0 2px 0 2px;font-size: 12px;border: 1px solid rgb(200,202,203)"><?= $product[$i]->amount ?></td>
                <td style="padding: 0 2px 0 2px;font-size: 12px;border: 1px solid rgb(200,202,203);width: 40px"><?= formatedPriceUSD($product[$i]->price) ?></td>
                <td style="padding: 0 2px 0 2px;font-size: 12px;border: 1px solid rgb(200,202,203)"><?= formatedPriceUSD($product[$i]->price * $product[$i]->amount) ?></td>
            </tr>
        <?php endfor;?>
    </tbody>
</table>
