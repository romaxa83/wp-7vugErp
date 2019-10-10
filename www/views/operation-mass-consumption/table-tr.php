<?php foreach ($models as $model) : ?>
<tr data-base-id="<?= $model->product_id ?>" data-variant-id="<?= $model->vproduct_id ?>" data-transaction-id="<?= $model->transaction->id ?>">
    <td><?= $model->vproduct_id == null ? $model->product->name : $model->product->name . $model->vproduct->chars ?></td>
    <td><?= $model->amount ?></td>
    <td><?= $model->transaction->getWhereagent()->one()->firm ?></td>
    <td>
        <a class="btn no-btn btn-delete-product-mass-consumption">
            <i class="fa fa-trash-o"></i>
        </a>
    </td>
</tr>
<?php endforeach; ?>
