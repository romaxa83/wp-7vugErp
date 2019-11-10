<?php 
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Html;
$this->title = "Корректировка товара";
?>
<div id="adjustment-content" class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <?php $form = ActiveForm::begin() ?>
                <div class="col-xs-4 adjustment-category form-group">
                    <?=  
                        Html::dropDownList('category','',[],[
                            'id' => 'product-category',
                            'class' => 'form-control product-category not-send',
                            'placeholder' => 'Выбрать категорию'
                        ]);
                    ?>
                </div>
                <div class="col-xs-4">
                    <div class="form-group choose-prod" data-type="true">
                        <?= 
                        Html::dropDownList('product_id','',[],[
                            'class' => 'form-control not-send'
                        ]); 
                        ?>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="col-xs-4">
                    <a class="btn btn-success adjustment-add">Добавить</a>
                </div>
                <?php ActiveForm::end() ?>
            </div>
            <form id='adjustment-form' method="post" action="/operation-adjustment/save-change">
                <div class="row">
                    <div class="col-xs-12">
                        <input id="form-token" type="hidden" name="<?=Yii::$app->request->csrfParam?>" value="<?=Yii::$app->request->csrfToken?>"/>
                        <table class="table table-striped hidden adjustment-table custom-table v3" data-float="<?= getFloat('usd') ?>">
                            <thead>
                                <tr>
                                    <th width="40%">Названия</th>
                                    <th width="10%">Количество</th>
                                    <th width="10%">Себестоимость ($)</th>
                                    <th width="10%">Оптовая цена ($)</th>
                                    <th width="10%">Цена прихода ($)</th>
                                    <th width="10%">Цена 1 ($)</th>
                                    <th width="10%" style="text-align: center;">Цена 2 ($)</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-3">
                        <button class="btn btn-success adjustment-save hidden">Редактировать</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
