<tr data-base-id="<?= $coming_product->product_id ?>" data-variant-id="<?= $coming_product->vproduct_id ?>">
    <td><?= $index ?></td>
    <td><?= $coming_product->vproduct_id === NULL ? $coming_product->product->vendor_code : $coming_product->vproduct->vendor_code ?></td>
    <td><?= $coming_product->product->category->name ?></td>
    <td><?= $coming_product->product->name ?></td>
    <td><?= $coming_product->vproduct_id === NULL ? 'Нет Характеристик' : $coming_product->vproduct->chars ?></td>
    <td class="live-edit" data-type="edit-coming" data-field="amount" data-type-data="number"><?= $coming_product->amount ?></td>
    <td class="live-edit" data-type="edit-coming" data-currency="ua" data-field="start_price_ua" data-type-data="float"><?= number_format($coming_product->start_price * getUsd(), getFloat('ua')) ?></td>
    <td class="live-edit" data-type="edit-coming" data-currency="usd" data-field="start_price" data-type-data="float"><?= number_format($coming_product->start_price, getFloat('usd')) ?></td>
    <td class="live-edit" data-type="edit-coming" data-currency="ua" data-field="price1" data-type-data="float"><?= number_format($coming_product->price1,2) ?></td>
    <td class="live-edit" data-type="edit-coming" data-currency="ua" data-field="price2" data-type-data="float"><?= number_format($coming_product->price2,2) ?></td>
    <td>
        <a class="btn no-btn btn-delete-product-coming">
            <i class="fa fa-trash-o"></i>
        </a>
    </td>
</tr>