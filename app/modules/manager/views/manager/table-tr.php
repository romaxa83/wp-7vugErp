<tr 
    class="row-product"
    data-product-id="<?= $one->product_id?>"
    data-vproduct-id="<?= $one->vproduct_id ? $one->vproduct_id : null?>"
>
    <td>
        <?= $one->product->category->name?>
    </td>
    <td class="product-name">
        <?= $one->vproduct_id ? $one->product->name .' '.VProduct::getCharValueFromId($one->vproduct->char_value) : $one->product->name?>
    </td>
    <td>
        <input type="number" class="form-control product-request-amount" min="0" value="<?= $one->amount?>">
    </td>
    <td>
        <?= formatedPriceUA($one->price)?>
    </td>
</tr>