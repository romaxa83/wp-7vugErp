<?php

use yii\helpers\Html;

/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\Operations */
/* @var $agents */
/* @var $stores */
/* @var $repository */
/* @var $products */
/* @var $categories */

$this->title = 'Расходная транзакция № ' . $model->transaction;
?>
<div class="content">
    <div class="row">
        <div class="col-xs-12"  style="margin-top: 20px">
            <p>Расходная транзакция от <span class="agent-name"><?= $repository[$model->whence] ?></span> на <?= $model['whereagent']->firm ?>
                за <?= $model->date ?> число. Транзакция (<span class="number-transaction"><?= $model->transaction ?></span>)</p>
        </div>
        <div class="form-edit-operation">
            <?= 
                $this->render('_form-where-whence', compact('model','repository','shop'));
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="table-form-product" style="margin-top: 40px" data-type-price="<?= $type_price['price_type'] ?>" data-float-usd="<?= getFloat('usd') ?>" data-float-uah="<?= getFloat('uah') ?>" data-currency-usd="<?= getUsd() ?>">
                <div class="">
                    <p>Добавление товаров на расход от <span class="agent-name"><?= $repository[$model->whence] ?></span> на <span class="store-name"><?= $model['whereagent']->firm ?></span> за <?= $model->date ?> число. Транзакция(<span class="number-transaction"><?= $model->transaction ?></span>)</p>
                </div>
                <?= 
                    $this->render('_form-add-product',[
                        'model' => $operConsumption,
                        'categories' => $categories
                    ])
                ?>
            </div>
            <div class="table-info">
                <h3>Транзакция <?= $model->transaction ?> <span class="summ"> --- <?= number_format($model->total_ua, getFloat('uah'),',','') ?> UAH/ <?= number_format($model->total_usd, getFloat('usd'),',','')  ?> $</span></h3>
                <?= 
                    $this->render('table-body', ['OperConsumption' => $OperConsumption]);
                ?>
            </div>
            <div class="btn-for-trans" style="margin-top: 30px">
                <?php if($model->status === 0): ?>
                    <?= Html::button('Сформировать транзакцию',['class' => 'ok-transaction snap'])?>
                    <?= Html::button('Отменить транзакцию',['data-id' => $model->id,'data-type' => 'consumption', 'class' => 'cancel-transaction snap _gray'])?>
                <?php elseif($model->status == 1): ?>
                    <?= Html::button('Сохранить',['class' => 'ok-transaction snap'])?>
                    <?= Html::a('Вернуться к списку','/operation/all-consumption',['class' => 'snap _gray'])?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
