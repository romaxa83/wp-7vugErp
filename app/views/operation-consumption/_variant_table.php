<h3 class="name_base_product" data-base-product-id="<?= $product->id?>">Вариации товара "<?= $product->name?>"</h3>
<div class="filter_variant_table_transaction">
    <div class="form-inline">
        <?php foreach ($char_values as $item):?>
            <?= yii\helpers\Html::dropDownList('chars', 'null', $item,[
                'class' => 'form-control filter_chars'
            ]);?>
        <?php endforeach;?>
        <?= yii\helpers\Html::button('Сброс фильтра',['class' => 'snap reset_filter_chars'])?>
    </div>
</div>
<table class="custom-table v3 table-var-prod">
    <thead>
        <tr>
            <th>Характеристики</th>
            <th>Кол-во на складе</th>
            <th>Кол-во</th>
            <th>Цена</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($v_products as $key => $v_product){ ?>
        <tr class="variant_product_row" data-id="<?= $v_product->id?>">
            <td class="string_chars" width="100%">
                <?= $v_product->chars ?>
            </td>
            <td width="100%" class="stock-amount-variant-consumption">
                <?= $v_product->amount ?>
            </td>
            <td width="100%">
                <input type="number" class="amount-var-prod-consumption" name="OperConsumptionVariant[<?= $key ?>][amount]" min="0" max="<?= $v_product->amount ?>" value="0">
            </td>
            <td width="100%">
                <input type="float" class="price-var-prod" name="OperConsumptionVariant[<?= $key ?>][price2]" min="0" value="<?= $typePrice == 1 ? $v_product->price1 : $v_product->price2 ?>">
            </td>
            <td style="display: none">
                <input name="OperConsumptionVariant[<?= $key ?>][product_id]" value="<?=$v_product->id?>">
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>