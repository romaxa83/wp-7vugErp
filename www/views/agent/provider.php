<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model */
/* @var $providers */

$this->title = 'Список поставщиков';
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="content">
    <div class="news-index">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 text-right">
                <?php if (Yii::$app->user->can('agent_create')):?>
                    <?= Html::button('Добавить нового поставщика',['class' => 'snap show-form','data-form'=>'agent','title' => 'Появится форма добавления поставщика'])?>
                    <?= Html::button('Отменить',['class' => 'hide-form snap _gray','data-form'=>'agent','title' => 'Убрать форму'])?>
                <?php endif;?>
            </div>
        </div>
        <div class="row">
            <div id="agent-form" class="col-md-12 hidden">
                <?php echo $this->render('_form-agent',[
                    'model_agent' => $model
                ]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-8 col-md-12">
                <?php foreach ($providers as $provider):?>
                    <div class="row next-row" style="margin-top: 0">
                        <div class="col-xs-6 col-md-7">
                            <div class="form-group">

                                <div class="input-group w-100">
                                    <a class="expand-plus form-control collapsed" style="width: 100%" data-toggle="collapse" href="#custom<?= $provider['id']?>" aria-expanded="false">
                                        <input style="width: 95%" type="text" value="<?= $provider['firm']?>" class="input-text" disabled="disabled" maxlength="255">
                                    </a>
                                    <div class="input-group-addon addon-transparent size2">
                                        <?php if (Yii::$app->user->can('agent_update')):?>
                                        <a href="/agent/update?id=<?= $provider['id']?>&type=1" type="button" class="btn no-btn" title="Редактировать <?= $provider['firm']?>"><i class="fa fa-pencil"></i></a>
                                        <?php endif;?>
                                        <?php if (Yii::$app->user->can('admin')):?>
                                            <input id="agent-status_<?= $provider['id']?>" class="change-status-agent" type="checkbox" <?= $provider['status'] == 1 ? 'checked' : null; ?>  data-id="<?= $provider['id']?>">
                                            <label for="agent-status_<?= $provider['id']?>" data-text-true="Вкл" data-text-false="Выкл"><i></i></label>
                                        <?php endif;?>
                                    </div>
                                </div>
                            </div>
                            <div class="collapse store-details" id="custom<?= $provider['id']?>">
                                <div class="form-group form-check level-2">
                                    <div class="input-group">
                                        <p>Адрес: <?= $provider['address']?$provider['address']:'<b>Адрес не указан</b>'?></p>
                                        <p>Представитель: <?= $provider['name']?$provider['name']:'<b>Представитель не указан</b>'?></p>
                                        <p>Телефон: <?= $provider['telephone']?$provider['telephone']:'<b>Телефон не указан</b>'?></p>
                                        <p>Реквизиты: <?= $provider['data']?$provider['data']:'<b>Реквизиты не указаны</b>'?></p>
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
