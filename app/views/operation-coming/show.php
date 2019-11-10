<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Operations;
use app\models\Agent;
use app\models\Category;
use kartik\select2\Select2;

/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\Operations */
/* @var $agents */
/* @var $stores */
/* @var $repository */
/* @var $products */
/* @var $categories */

$this->title = 'Приходная накладная № ' . $model->transaction;
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12"  style="margin-top: 20px">
            <p>Приходная транзакция от <span class="agent-name"><?= $model['whenceagent']->firm ?></span> на <?= $repository[$model->where] ?>
                за <?= $model->date ?> число. Транзакция (<span class="number-transaction"><?= $model->transaction ?></span>)</p>
        </div>
        <div class="form-edit-operation">
            <?=
                $this->render('_form-where-whence', compact('model','agents','repository'));
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="table-to-product" style="margin-top: 40px">
                <div class="">
                    <p>Добавление товаров от <span class="agent-name"><?= $model['whenceagent']->firm ?></span> на <?= $repository[$model->where] ?>
                        за <?= $model->date ?> число. Транзакция (<span class="number-transaction"><?= $model->transaction ?></span>)</p>
                </div>
                <?=
                    $this->render('_form-add-product', [
                        'model' => $oper_coming,
                        'categories' => $categories,
                        'transaction_id' => $model->id
                    ]);
                ?>
            </div>
            <div class="table-info">
                <h3>Транзакция <?= $model->transaction ?> <span class="summ"> --- <?= number_format($model->total_ua, getFloat('uah'),',','') ?> UAH/ <?= number_format($model->total_usd, getFloat('usd'),',','')  ?> $</span></h3>
                <?=
                    $this->render('table-body',[
                        'coming_products' => $coming_products
                    ]);
                ?>
            </div>
            <div class="btn-for-trans" style="margin-top: 30px">
                <?php if($model->status === 0): ?>
                    <?= Html::button('Сформировать транзакцию',['class' => 'ok-transaction snap'])?>
                    <?= Html::button('Отменить транзакцию',['data-id' => $model->id,'data-type' => 'coming', 'class' => 'cancel-transaction snap _gray'])?>
                <?php elseif($model->status == 1): ?>
                    <?= Html::button('Сохранить',['class' => 'ok-transaction snap'])?>
                    <?= Html::a('Вернуться к списку','/operation/all-coming',['class' => 'snap _gray'])?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
