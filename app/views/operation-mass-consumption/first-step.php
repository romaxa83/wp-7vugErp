<h4>Расприделения товара по магазинам</h4>
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
        <tr data-key-base="<?= $product['id'] ?>" data-transaction="<?= $transaction[$i]['id'] ?>" data-balance="<?= $product['amount']-array_sum($amount) ?>">
            <td>
                <?= $transaction[$i]['whereagent']['firm'] ?>
            </td>
            <td>
                <?= $product['name'] ?>
            </td>
            <td>
                <input type="number" class="form-control amount-mass-consumption" value="<?= $amount[$i] ?>" min="0">
            </td>
            <td>
                <?php $price = number_format($product['price'.$transaction[$i]['whereagent']['price_type']], getFloat('uah'),',','');  ?>
                <input type="float" class="form-control price-mass-consumption" value="<?= $price ?>">
            </td>
        </tr>
        <?php endfor; ?>
    </tbody>
</table>
<div>
    <button class="snap confirm-mass mr-t-10">Подтвердить</button>
</div>