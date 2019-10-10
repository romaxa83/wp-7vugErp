<tr data-product="<?= $product[0]['product_id'] ?>">    
    <td colspan="16">
        <table class="table-fix custom-table v3 table-view-var-prod">
            <thead>
            <tr>
                <th>Характеристики</th>
                <th>Кол-во</th>
                <th>P1(uah)</th>
                <th>P2(uah)</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
                <?php foreach($product as $one){ ?>
                    <tr data-base-id="<?= $product[0]['product_id'] ?>" data-variant-id="<?= $one['id'] ?>">
                        <td width="100%"><?= $one['chars'] ?></td>
                        <td class="live-edit" data-type="edit-vproduct-catalog" data-field="amount" data-type-data="number"><?= $one['amount'] ?></td>
                        <td class="live-edit" data-type="edit-vproduct-catalog" data-field="price1" data-type-data="float"><?= $one['price1'] ?></td>
                        <td class="live-edit" data-type="edit-vproduct-catalog" data-field="price2" data-type-data="float"><?= $one['price2'] ?></td>
                        <td class="element-check" width="100%">
                            <input class="custom-checkbox publish_status" value="<?= $one['id'] ?>" data-id="<?= $one['id'] ?>"
                                   data-type="vproduct" type="checkbox" <?= $one['publish_status'] == 1 ? 'checked' : '' ?>
                                   <?= $one['disable'] == 1 ? 'disabled' : '' ?> title="Статус для магазина">
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </td>
</tr>