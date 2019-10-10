<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use app\models\CharacteristicValue;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CharacteristicSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $values */
/* @var $model app\models\Characteristic */

$this->title = 'Характеристики';
$this->params['breadcrumbs'][] = $this->title;
?>


<?php
Modal::begin([
    'header' => '<h2>Удаление не возможно</h2>',
    'toggleButton' => ['label' => 'click me', 'id' => 'toggle-modal', 'class' => 'hidden'],
    'closeButton' => ['id' => 'close-button'],
    'footer' => '<button type="button" class="btn btn-default pull-right close-modal" data-dismiss="modal">Ok</button>'
]);
?>
<h3>Данная характеристика используется у товаров:</h3>
<div class="products-name-modal">

</div>
<?php Modal::end(); ?>
<style>
    #box-characteristic{
        margin-top: 20px;
    }
</style>
<div class="characteristic-index">
    <section class="content">
        <div class="news-index">
            <div class="row">
                <?php if (Yii::$app->user->can('characteristic_create')):?>
                    <div class="col-xs-12 text-right control-btn">
                        <?= Html::Button('Добавить характеристику', ['class' => 'snap show-form','data-form' => 'characteristic-block']) ?>
                        <?= Html::Button('Отменить', ['class' => 'snap _gray hide-form', 'data-form' => 'characteristic-block']) ?>
                    </div>
                    <div id="characteristic-block-form" class="col-xs-12 hidden">
                        <?= $this->render('_form-characteristic', [
                            'model' => $model,
                        ]); ?>
                    </div>
                <?php endif;?>
            </div>

            <div id="box-characteristic">
                <?php $characteristics = ArrayHelper::map($values, 'id', 'name'); ?>

                <div class="row">
                    <div class="col-xs-12">
                        <?php foreach ($characteristics as $id => $characteristic): ?>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="expand-plus form-control collapsed" data-toggle="collapse" href="#custom<?= $id; ?>" aria-expanded="false"><?= $characteristic; ?></span>
                                    <div class="input-group-addon addon-transparent size3">
                                        <?php if (Yii::$app->user->can('characteristic_update')):?>
                                            <a href="<?= Url::toRoute('characteristic/update?id=' . $id, true); ?>" class="btn no-btn update-char"><i class="fa fa-pencil"></i></a>
                                        <?php endif;?>
                                        <?php if (Yii::$app->user->can('admin')):?>
                                            <a href="<?= Url::toRoute('characteristic/delete?id=' . $id, true); ?>" data-category-id="<?= $id?>" class="btn no-btn remove-char"><i class="fa fa-trash-o"></i></a>
                                        <?php endif;?>
                                    </div>
                                </div>
                            </div>
                            <div class="collapse" id="custom<?= $id; ?>" data-id="<?= $id; ?>" aria-expanded="false" style="height: 0px;">
                                <div class="characteristic-list">
                                    <?php
                                    $sub = CharacteristicValue::find()->where(['id_char' => $id])->asArray()->all();
                                    $sub_characteristic = ArrayHelper::map($sub, 'id', 'name'); ?>
                                    <?php foreach ($sub_characteristic as $sub_id => $sub_characteristic): ?>
                                        <div class="form-group level-2">
                                            <div class="input-group">
                                                <span class="expand-plus form-control" data-toggle="collapse" href="#custom12" aria-expanded="true"><?= $sub_characteristic ?></span>
                                                <div class="input-group-addon addon-transparent size3">
                                                    <?php if (Yii::$app->user->can('characteristic_update')):?>
                                                        <a href="<?= Url::toRoute('characteristic-value/update?id=' . $sub_id, true); ?>" type="button" class="btn no-btn update-characteristic"><i class="fa fa-pencil"></i></a>
                                                    <?php endif;?>
                                                    <?php if (Yii::$app->user->can('admin')):?>
                                                        <a href="<?= Url::toRoute('characteristic-value/delete?id=' . $sub_id, true); ?>" type="button" class="btn no-btn remove-characteristic"><i class="fa fa-trash-o"></i></a>
                                                    <?php endif;?>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endforeach; ?>
                                </div>
                                <div class="form-group level-2">
                                    <?php if (Yii::$app->user->can('characteristic_create')):?>
                                        <div class="input-group">
                                            <a class="expand-plus form-control add-characteristic" href="<?= Url::toRoute('characteristic-value/template', true); ?>" aria-expanded="true">Добавить заначение</a>
                                            <div class="input-group-addon addon-transparent size3"></div>
                                        </div>
                                    <?php endif;?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>