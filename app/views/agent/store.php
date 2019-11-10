<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $stores */
/* @var $model */

$this->title = 'Магазины';
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="content">
    <div class="news-index">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 text-right">
                <?php if (Yii::$app->user->can('agent_create')):?>
                <?= Html::button('Добавить новый магазин',['class' => 'show-form snap','data-form' => 'agent','title' => 'Появится форма добавления магазина'])?>
                <?= Html::button('Отменить',['class' => 'hide-form snap _gray','data-form' => 'agent','title' => 'Убрать форму'])?>
                <?php endif;?>
            </div>
        </div>
        <div class="row">
            <div id="agent-form" class="col-md-12 hidden">
                <?php 
                    echo $this->render('_form-agent',[
                       'model_agent' => $model 
                    ]);
                ?>
            </div>
        </div>
        <div class="row next-row">
            <div class="col-xs-12 col-sm-8 col-md-12">
                <?php foreach ($stores as $store):?>
                    <div>
                        <div class="col-xs-6 col-md-7 store-group">
                            <div class="form-group">
                                <div class="input-group w-100">
                                    <a class="expand-plus form-control collapsed" data-toggle="collapse" href="#custom<?= $store['id']?>" aria-expanded="false">
                                        <input type="text" style="width: 95%;" value="<?= $store['firm']?>" class="input-text" disabled="disabled">
                                    </a>
                                    <div class="input-group-addon addon-transparent size2">
                                        <?php if (Yii::$app->user->can('agent_update')):?>
                                        <a href="/agent/update?id=<?= $store['id']?>&type=2" type="button" class="btn no-btn" title="Редактировать <?= $store['firm']?>"><i class="fa fa-pencil"></i></a>
                                        <?php endif;?>
                                        <?php if (Yii::$app->user->can('admin')):?>
                                            <input id="agent-status_<?= $store['id']?>" class="change-status-agent" type="checkbox" <?= $store['status'] == 1 ? 'checked' : null; ?>  data-id="<?= $store['id']?>">
                                            <label for="agent-status_<?= $store['id']?>" data-text-true="Вкл" data-text-false="Выкл"><i></i></label>
                                        <?php endif;?>
                                    </div>
                                </div>
                            </div>
                            <div class="collapse store-details" id="custom<?= $store['id']?>">
                                <div class="form-group form-check level-2">
                                    <div class="input-group">
                                        <p>Адрес: <?= $store['address']?$store['address']:'<b>Адрес не указан</b>'?></p>
                                        <p>Менеджер: <?= !empty($store['name'])?$store['name']:'<b>Менеджера нет</b>'?></p>
                                        <p>Телефон: <?= $store['telephone']?$store['telephone']:'<b>Телефон не указан</b>'?></p>
                                        <p>Реквизиты: <?= $store['data']?$store['data']:'<b>Реквизиты не указаны</b>'?></p>
                                        <p>Тип цены: <?= $store['price_type'] == 1?'Цена 1':'Цена 2';?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-lg-offset-1 col-md-3">
                            <div class="form-group">
                                <div class="input-group w-100">
                                    <select class="price-for-<?= $store['id']?> custom-select w-100">
                                        <option value="<?= $store['price_type']?>"><?= $store['price_type'] == 1?'Цена 1':'Цена 2'?></option>
                                        <option value="<?= $store['price_type'] == 1?2:1?>"><?= $store['price_type'] != 1?'Цена 1':'Цена 2'?></option>
                                    </select>
                                    <div class="input-group-addon addon-transparent size3">
                                        <button id="<?= $store['id']?>" type="button" class="new-price btn no-btn" title="Установить новую цену для магазины <?= $store['firm']?>">
                                            <i class="fa fa-floppy-o"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach;?>
            </div>
        </div>
    </div>
</section>
