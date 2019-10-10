<?php
use yii\helpers\Html;
?>
<h3>Товары заявки</h3>
<table class="custom-table v3" data-request-id="<?= $request->id?>">
    <thead>
    <tr>
        <th>Категория</th>
        <th>Товар</th>
        <th>Кол-во</th>
        <th>Цена</th>
    </tr>
    </thead>
    <tbody class="table-request-body" data-request-id="<?= $request->id?>">
    <?php if (isset($request->products) && !(empty($request->products))):?>
        <?php foreach ($request->products as $one) : ?>
            <?= $this->render('table-tr',compact('one','request')); ?>
        <?php endforeach;?>
    <?php endif;?>
    </tbody>
</table>
<div class="form-group top-20">
    <?= Html::label('Комментарии к заявке',['for' => 'comment-request'])?>
    <?= Html::textArea('comment-request',
        $request->comment != null ? $request->comment:'',
        [
            'id'=>'comment-request',
            'class' => 'form-control',
        ]); ?>
</div>
<div class="form-group text-center content">
    <?= Html::button('Сформировать заявку',['class' => 'snap confirm-product-request','style' => 'margin-top:15px'])?>
</div>