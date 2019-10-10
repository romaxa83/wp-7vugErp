<tr data-base-id="<?= $OperConsumption->product_id ?>" data-variant-id="<?= $OperConsumption->vproduct_id ?>">
    <td><?= $index ?></td>
    <td><?= $OperConsumption->product->vendor_code; // $OperConsumption->vproduct_id === NULL ? $OperConsumption->product->vendor_code : $OperConsumption->vproduct->vendor_code ?></td>
    <td><?= $OperConsumption->product->category->name ?></td>
    <td><?= $OperConsumption->product->name ?></td>
    <td></td>
    <td class="live-edit" data-type="edit-consumption" data-field="amount" data-type-data="number"><?= $OperConsumption->amount ?></td>
    <td class="live-edit" data-type="edit-consumption" data-currency="ua" data-field="price" data-type-data="float"><?= number_format($OperConsumption->price, getFloat('ua') , '.' , '') ?></td>
    <td>
        <a class="btn no-btn btn-delete-product-consumption">
            <i class="fa fa-trash-o"></i>
        </a>
    </td>
</tr>
