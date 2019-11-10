<?php 
    $this->title = 'Массовая расходная транзакция';
    use yii\helpers\Html;
?>
<div class="content">
    <div class="table-form-product-mass" style="margin-top: 40px" data-type-price="<?= $type_price['price_type'] ?>" data-float-usd="<?= getFloat('usd') ?>" data-float-uah="<?= getFloat('uah') ?>" data-currency-usd="<?= getUsd() ?>">
        <?=  $this->render('_form-add-product',[
            'categories' => $categories,
            'product' => $product,
            'operConsumption' => $operConsumption,
            'id' => $id,
            'type_price' => $type_price
        ]); ?>
    </div>
    <div id="table-first-step"></div>
    <div id="table-for-products">
        <table class="custom-table v3 mr-bt-10 table-fix">
            <thead>
                <tr>
                    <th>Товар</th>
                    <th>Кол-во</th>
                    <th>Магазин</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?= 
                $this->render('table-tr', ['models' => $models]);
            ?>
            </tbody>
        </table>
        <div class='mr-t-10'>
            <?= Html::button('Сформировать транзакцию',['class' => 'ok-mass-transaction snap'])?>
            <?= Html::button('Отменить транзакцию',['data-type' => 'mass-consumption','data-id' => implode(',', Yii::$app->request->get('id')) ,'class' => 'cancel-transaction snap _gray'])?>
        </div>  
    </div>
    <div id="block-for-balance"></div>
</div>
