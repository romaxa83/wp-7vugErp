<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use kartik\select2\Select2;
use yii\web\JsExpression;


$stepInput = '0.' . str_pad('0', getFloat('ua') - 1, '0') . '1';
?>
<div class="content">
    <div class="news-index">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <?php if (!Yii::$app->request->get('id')):?>
                            <div id="category-form" class="hidden form-active form-category-from-product">
                                <?= $this->render('../category/_form-category', [
                                    'model_cat' => $category_form[0],
                                    'parent_cats' => $categories,
                                    'chars_cat' => $category_form[1],
                                ]); ?>
                            </div>
                            <div id="agent-form" class="hidden form-active">
                                <?= $this->render('../agent/_form-agent', [
                                    'model_agent' => $model_agent,
                                ]); ?>
                            </div>
                        <?php endif;?>
                    </div>
                </div>
                <?php $form = ActiveForm::begin([
                    'action' => (Yii::$app->controller->action->id == 'update') ? '/product/update?id='  . Yii::$app->request->get('id') : '/product/create'
                ])?>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="add_new_header row">
                                <div class="col-sm-4">
                                    <?= $form->field($model,'vendor_code')->textInput(['readonly' => true,'value' => $model->isNewRecord ? '000000000' : $model->vendor_code, $model->isNewRecord ? '' : 'data-id' => $model->id ])->label('')?>
                                </div>
                            <?php if(!Yii::$app->request->get('id')):?>
                            <div class="col-sm-4">
                                <?= $form->field($model,'is_variant')->textInput()->dropDownList([
                                    '1' => 'Обычный товар',
                                    '2' => 'Вариативный товар'
                                ])->label('');
                                ?>
                            </div>
                            <?php endif;?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="add_new_header row">
                            <?php if(!Yii::$app->request->get('id')):?>
                            <div class="col-sm-10">
                                <div class="row hidden-xs">
                                    <div class="col-sm-6">
                                    <?php if (Yii::$app->user->can('agent_create')):?>
                                        <div class="add_new_buttons">
                                            <button class="snap form-control show-form" data-form="agent">Добавить поставщика</button>
                                        </div>
                                    <?php endif;?>
                                    </div>
                                    <div class="col-sm-6">
                                    <?php if (Yii::$app->user->can('category_create')):?>
                                        <div class="add_new_buttons">
                                            <button class="snap form-control show-form" data-form="category">Добавить категорию</button>
                                        </div>
                                    <?php endif;?>
                                    </div>
                                </div>
                            </div>
                            <?php endif;?>
                            <div class="col-sm-2 pull-right">
                                <div class="form-group field-product-status">
                                    <input id="product-status" name="Product[status]" value="1" type="checkbox" <?= $model->status == 1 ? 'checked' : ''?>>
                                    <label for="product-status" data-text-true="Вкл" data-text-false="Выкл"><i></i></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="product-form">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="add_new_product">
                                        <div class="add_new_body">
                                            <div class="name-base-product">
                                                <?php 
                                                    echo $form->field($model, 'name')->widget(Select2::classname(), [
                                                        'initValueText' => $model->name,
                                                        'options' => ['placeholder' => 'Введите название товара', 'class'=>'settlement-select'],
                                                        'pluginOptions' => [
                                                            'allowClear' => true,
                                                            'minimumInputLength' => 3,
                                                            'language' => [
                                                                'errorLoading' => new JsExpression("function () { return 'Загрузка результатов'; }"),
                                                            ],
                                                            'ajax' => [
                                                                'url' => '/product/all-product',
                                                                'async' => false,
                                                                'dataType' => 'json',
                                                                'data' => new JsExpression('function(params) { return {name:params.term}; }')
                                                            ],
                                                            'tags' => true,
                                                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                                            'templateResult' => new JsExpression('function(city) { return city.text; }'),
                                                            'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                                                        ],
                                                    ]);
                                                ?>
                                            </div>
                                            <div class="name-variant-product">
                                                <?php 
                                                    echo $form->field($model, 'name',['template' => "{label}\n{input}"])->dropDownList([]);
                                                ?>
                                            </div>
                                            <div class="input_custom">
                                                <?=
                                                    $form->field($model, 'category_id')->widget(Select2::classname(), [
                                                        'data' => $categories,
                                                        'language' => Yii::$app->language,
                                                        'options' => [
                                                                'placeholder' => 'Выберите категорию',
                                                                'class' => 'form-control'
                                                        ],
                                                        'pluginOptions' => [
                                                            'allowClear' => true
                                                        ],
                                                    ]);
                                                ?>
                                            </div>
                                            <?php if (!Yii::$app->request->get('id')):?>
                                            <div class="input_custom">
                                                <div class="form-group var-prod-sel field-product-name-sel required"></div>
                                            </div>
                                            <?php endif;?>
                                            <div class="input_custom">
                                                <?=
                                                $form->field($model, 'agent_id')->widget(Select2::classname(), [
                                                    'data' => $agents,
                                                    'language' => Yii::$app->language,
                                                    'options' => [
                                                        'placeholder' => 'Выберите поставщика',
                                                        'class' => 'form-control'
                                                    ],
                                                    'pluginOptions' => [
                                                        'allowClear' => true
                                                    ],
                                                ]);
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group input_custom_collection">
                                            <label for="list-characteristics" class="hidden label-for-characteristics">Список характеристик:</label>
                                            <div class="check-ajax"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="add_new_product">
                                        <div class="input_custom input_custom_wrapper clearfix">
                                            <div class="form-group input_custom">
                                                <?= $form->field($model, 'min_amount')->textInput(['type' => 'number','min' => 0, 'value' => $model->min_amount == null ? 0 : $model->min_amount])->label('Мин. кол-во на складе') ?>
                                            </div>
                                        </div>
                                        <div class="input_custom input_custom_wrapper clearfix">
                                            <div class="form-group input_custom w-50 _left">
                                                <?= $form->field($model, 'unit')->textInput()->dropDownList([
                                                    'шт.' => 'шт.',
                                                    'кг.' => 'кг.',
                                                    'уп.' => 'уп.',
                                                    'л.' => 'л.',
                                                ])  ?>
                                            </div>
                                            <div class="form-group input_custom w-50 _right">
                                                <?= $form->field($model, 'margin')->textInput(['type' => 'number','min' => 0,'value' => $model->margin == null ? 20 : $model->margin])->label('Маржа на товар (%)') ?>
                                            </div>
                                        </div>
                                        <div class="input_custom input_custom_wrapper clearfix">
                                            <div class="form-group input_custom w-50 _left">
                                                <?= $form->field($model, 'price1')->textInput(['type' => 'number','step' => $stepInput])->label('Розничная цена 1 (UAH)') ?>
                                            </div>
                                            <div class="form-group input_custom w-50 _right">
                                                <?= $form->field($model, 'price2')->textInput(['type' => 'number','step' => $stepInput])->label('Розничная цена 2 (UAH)') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="new_prodact_buttons inline row">
                                        <?= Html::submitButton($model->isNewRecord ? 'Сохранить' : 'Редактировать', ['class' => 'snap col-sm-4 save-prod']) ?>
                                        <?= Html::a('Отмена',Url::toRoute('product/index',true),['class' => 'snap _gray js-add-item col-sm-4'])?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end()?>
            </div>
        </div>
    </div>
</div>
<div class="noty_layout"></div>
<div class="block-for-variant content"></div>