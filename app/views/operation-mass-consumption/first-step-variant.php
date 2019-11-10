<table class="custom-table v3 mr-bt-10 table-fix">
    <thead>
        <tr>
            <th>Магазин</th>
            <th>Товар</th>
            <th>Кол-во</th>
            <th>Цена</th>
        </tr>
    </thead>
    <tbody>
        <?php for($i=0;$i<count($transaction);$i++) : ?>
            <?php for($p=0;$p<count($vproduct);$p++) : ?>
        <tr data-key-base="<?= $product['id'] ?>" data-key-variant="<?= $vproduct[$p]['id'] ?>" data-balance="<?= $vproduct[$p]['amount'] - array_sum($amount[$p]) ?>"  data-transaction="<?= $transaction[$i]['id'] ?>">
                    <td>
                        <?= $transaction[$i]['whereagent']['firm'] ?>
                    </td>
                    <td>
                        <?= $product['name'] . ' ' . $vproduct[$p]['char_value'] ?>
                    </td>
                    <td>
                        <input type="number" class="form-control amount-mass-consumption" value="<?= $amount[$p][$i] ?>" min="0">
                    </td>
                    <td>
                        <?php $price = number_format($product['price'.$transaction[$i]['whereagent']['price_type']], getFloat('uah'),',','');  ?>
                        <input type="float" class="form-control price-mass-consumption" value="<?= $price ?>">
                    </td>
                </tr>
            <?php endfor; ?>
        <?php endfor; ?>
    </tbody>
</table>
<div>
    <button class="snap confirm-mass">Подтвердить</button>
</div>