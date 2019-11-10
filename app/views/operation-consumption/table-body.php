<table
    id="coming-goods"
    class="table-fix table-transaction custom-table v3"
>
    <thead>
    <tr>
        <th>#</th>
        <th width="5%">Артикул</th>
        <th width="7%">Категория</th>
        <th width="50%">Товар</th>
        <th width="7%">Характеристики</th>
        <th width="5%">Кол-во</th>
        <th width="5%">Цена для магазина(uah)</th>
        <th width="5%"></th>
    </tr>
    </thead>
    <tbody>
        <?php
        $i = 0;
        foreach ($OperConsumption as $OneOperConsumption) : $i++;
        ?>
            <?= $this->render('table-tr', [
                'OperConsumption' => $OneOperConsumption,
                'index' => $i
            ]) ?>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>