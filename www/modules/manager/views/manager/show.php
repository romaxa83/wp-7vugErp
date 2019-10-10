<?php
use app\modules\manager\ManagerAsset;

/* @var $this yii\web\View */
/* @var $request app\modules\Manager\models\Request */
/* @var $categories */
/* @var $prod_value */

$this->title = 'Заявка для магазина - ' . $request->store->firm;
$this->params['breadcrumbs'][] = $this->title;

ManagerAsset::register($this);
?>
<div 
    class="product-request-index content"
    data-request-id="<?= $request->id ?>"
    data-store-id="<?= $request->store->id ?>"
    data-store-price="<?= $request->store->price_type ?>"
>
    <div class="add-product-form">
        <?= $this->render('_form-add-product',compact('model','request')); ?>
    </div>
    <div class="adjustment-product-form"></div>
    <div class="product-for-manager collapse">
       <?= $this->render('_table-product-request',compact('model','request')); ?>
    </div>
</div>