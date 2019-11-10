<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$EditMode = Yii::$app->session->get('EditMode');
$this->title = 'Товары';
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="content">
    <?php if (Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'product_create') || Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'product_update')) : ?>
        <div class="row">
            <div class="col-sm-9 hidden-xs">
                <div class="control-panel">
                    <?= Html::button('Экспорт',['class' => 'show-panel snap', 'data-panel' => 'export', 'title' => 'Экспорт товаров'])?>
                    <?= Html::button('Импорт',['class' => 'show-panel snap', 'data-panel' => 'import', 'title' => 'Импорт товаров'])?>
                    <?= Html::button('Фильтр',['class' => 'show-panel snap', 'data-panel' => 'filter', 'title' => 'Фильтр'])?>
                    <?= Html::button('Печать',['class' => 'show-panel snap', 'data-panel' => 'print', 'title' => 'Печать'])?>
                    <input id="dev-env" class="switch-edit-mode" type="checkbox" <?= $EditMode == true ? 'checked' : null ?>>
                    <label for="dev-env" data-text-true="Вкл" data-text-false="Выкл" title="Включить режим редактирования"><i></i></label>
                </div>
                <!-- форма фильтра -->
                <div id="filter-panel" class="hidden panel">
                    <?= $this->render('_search', [
                        'searchModel' => $searchModel,
                        'agents_filter' => $agents_filter
                    ]); ?>
                </div>
                <!-- форма импорта -->
                <div id="import-panel" class="hidden panel">
                    <?php $form = ActiveForm::begin([
                        'options' => ['enctype'=>'multipart/form-data'],
                        'action' => Url::toRoute('product/upload'),
                        'method' => 'POST'
                    ]); ?>
                    <div class="input-file"><div class="img-input">Выбрать файл <i class="fa fa-upload"></i></div>
                        <?= $form->field($model_csv,'file')->fileInput(['class' => 'snap inline'])->label('') ?>
                    </div>
                    <div class="form-group">
                        <?= Html::submitButton('Загрузить',['class'=>'snap']) ?>
                        <?= Html::button('Отмена',['class' => 'hide-panel snap _gray', 'title' => 'Отменить импорт товаров'])?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
                <!-- форма экспорта -->
                <div id="export-panel" class="hidden panel">
                    <?= Html::button('MS Excel',['class'=>'snap export excel_export','data-count'=>$countPage,'data-type'=>'excel']) ?>
                    <?= Html::button('CSV',['class'=>'snap export csv_export','data-count'=>$countPage,'data-type'=>'csv']) ?>
                    <?= Html::button('Отмена',['class' => 'hide-panel snap _gray', 'title' => 'Отменить экспорт товаров'])?>
                </div>
                <!-- форма печати -->
                <div id="print-panel" class="hidden panel">
                    <?= Html::a('Печать товаров', ['/product/print-product-pdf'], [
                        'class'=>'snap',
                        'target'=>'_blank',
                        'data-toggle'=>'tooltip',
                        'title'=>'Печать товаров',
                        'data-pjax' => 0
                    ]);?>
                    <?= Html::a('Печать переоценки', ['/product/print-change-price-pdf'], [
                        'class'=>'snap',
                        'target'=>'_blank',
                        'data-toggle'=>'tooltip',
                        'title'=>'Печать переоценки',
                        'data-pjax' => 0
                    ]);?>
                    <?= Html::button('Отмена',['class' => 'hide-panel snap _gray', 'title' => 'Отменить'])?>
                </div>
            </div>
            <?php if (Yii::$app->user->can('product_create')):?>
                <div class="col-xs-12 col-sm-3 text-right control-form">
                    <?= Html::button('Добавить товар',['class' => 'show-form snap', 'data-form' => 'product', 'title' => 'Появится форма добавления товара'])?>
                    <?= Html::button('Отменить',['class' => 'hide-form snap _gray', 'data-form' => 'product', 'title' => 'Убрать форму'])?>
                </div>
            <?php endif;?>
        </div>
    <?php endif;?>
    <!-- форма добавления товара -->
    <div id="product-form" class="row hidden">
        <?= $this->render('_form-product',[
            'model' => $model,
            'categories' => $categories,
            'category_form' => $category_form,
            'model_agent' => $model_agent,
            'agents' => $agents
        ]) ?>
    </div>
    <?php if (Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'product_create') || Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'product_update')) : ?>
    <!-- таблица товара админ -->
        <div class="col-xs-12">
            <div class="product-index">
                <?= $this->render('_product-table-admin', [
                    'dataProvider' => $dataProvider,
                    'model' => $model,
                    'agents' => $agents
                ]); ?>
            </div>
        </div>
    <?php else:?>
    <!-- таблица товара менеджеры -->
        <div class="col-xs-12">
            <div class="product-index">
                <?= $this->render('_product-table-manager', [
                    'dataProviderManager' => $dataProviderManager,
                    'model' => $model,
                    'agents' => $agents
                ]); ?>
            </div>
        </div>
    <?php endif;?>
</section>