<?php
use yii\helpers\Html;
use app\modules\manager\ManagerAsset;

/* @var $model app\modules\manager\models\Request */

$this->title = "Заявка от магазина ".$model->store->firm;
ManagerAsset::register($this);
?>
<div class="content">
    <div class="row">
        <div class="col-xs-4">
            <h4>Список товаров по заявке от магазина "<?= $model->store->firm?>"</h4>
        </div>
        <div class="col-xs-2">
            <h4>Дата : <?=  date("d.m.Y H:i:s",$model->updated_at) ?></h4>
        </div>
        <div class="col-xs-1 pull-right">
            <?= Html::a('Назад',['/manager/admin/index'],['class'=>'btn btn-default']); ?>
        </div>
        <div class="col-xs-4">
            <?= Html::button('Товары с остатком 0 на складе',['class' => 'snap pull-right','id' => 'empty-in-stock']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h4>Магазин : "<?= $model->store->firm ?>"  Менеджер : "<?= $model->store->name != null ? $model->store->name : 'Менеджера нет'; ?>"</h4>
        </div>
    </div>
    <table class="table-fix custom-table v3 table-product-request-admin" data-store-id="<?= $model->store_id?>">
        <thead>
        <tr>
            <th>Категория</th>
            <th>Товар</th>
            <th>На складе</th>
            <th>Кол-во по всем заявкам</th>
            <th><?= Yii::$app->user->can('admin') ? 'Кол-во по заявке' : 'Кол-во';?></th>
            <th>Цена(ua)</th>
            <th><input type="checkbox" class="confirm-all-product">Потвердить</th>
            <th class="clear-request" data-request-id="<?= $model->id?>">Удалить</th>
            <th></th>
        </tr>
        </thead>
        <tbody class="table-request-body"  data-request-id="<?= $model->id?>">
        <?php if (isset($product) && !empty($product)):?>
            <?php foreach ($product as $one): ?>
                <tr class="add-row-prod <?= ($one['amountStock'] == 0) ? 'hidden' : '' ?>"
                    data-request-id="<?= $one['request_id'] ?>"
                    data-product-id="<?= $one['product_id'] ?>"
                    data-vproduct-id="<?= $one['vproduct_id'] ?>">
                    <td><?= $one['categoryName'] ?></td>
                    <td><?= $one['vproduct_id'] ? ($one['productName'] .' '. $one['chcaracteristic']) : $one['productName'] ?></td>
                    <td class="amount-on-stock" data-value="<?= $one['amountStock'] ?>"><?= $one['amountStock'] ?></td>
                    <td class="all-request-amount">
                        <div class="tooltip"><span class="value"><?= $model->countProductRequests($one['product_id'],$one['vproduct_id']) ?></span>
                            <?php if(!empty($model->anotherStore($one['product_id'],$one['vproduct_id']))) : ?>
                                <span class="tooltiptext">
                                <?php
                                foreach ($model->anotherStore($one['product_id'],$one['vproduct_id']) as $oneStore){
                                    echo $oneStore['firm'] . ' | ' . $oneStore['amount'] . '</br>';
                                }
                                ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td><input type="number" class="form-control product-request-amount" min="0" value="<?= $one['amount'] ?>"></td>
                    <td><?= formatedPriceUA($one['price']) ?></td>
                    <td><input type="checkbox" class="confirm-product"></td>
                    <td><i class="fa fa-trash-o remove-product-request"></i></td>
                    <td></td>
                </tr>
            <?php endforeach;?>
        <?php endif;?>
        </tbody>
    </table>
    <div class="form-group top-20">
        <?= Html::label('Комментарии к заявке',['for' => 'comment-request'])?>
        <?= Html::textArea('comment-request',
            $model->comment != null ? $model->comment : '',
            [
                'id'=>'comment-request',
                'class' => 'form-control',
            ]); ?>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= Html::button('Сформировать транзакцию',['class' => 'snap create_transaction','style' => 'margin-top:15px'])?>
        </div>
    </div>
</div>
