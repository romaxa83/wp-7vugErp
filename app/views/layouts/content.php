<?php
use yii\widgets\Breadcrumbs;
use dmstr\widgets\Alert;

?>
<div class="content-wrapper clearfix" data-float-usd="<?= getFloat('usd') ?>" data-float-uah="<?= getFloat('uah') ?>">
    <section class="content-header">
        <?php if (isset($this->blocks['content-header'])) { ?>
            <h1><?= $this->blocks['content-header'] ?></h1>
        <?php } else { ?>
            <h1>
                <?php
                if ($this->title !== null) {
                    echo \yii\helpers\Html::encode($this->title);
                } else {
                    echo \yii\helpers\Inflector::camel2words(
                        \yii\helpers\Inflector::id2camel($this->context->module->id)
                    );
                    echo ($this->context->module->id !== \Yii::$app->id) ? '<small>Module</small>' : '';
                } ?>
            </h1>
        <?php } ?>

        <?=
        Breadcrumbs::widget(
            [
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]
        ) ?>
    </section>

    <?= Alert::widget() ?>
    <?= $content ?>
</div>

<footer class="main-footer"></footer>
    <!-- SideBar Chat -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- element control -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
        <li>
            <button id="hide-chat" type="button" class="btn btn-default">
                Спрятать элемент
            </button>
        </li>
    </ul>
    <!-- Chat -->
    <div id="chat-content" class="text-danger text-center">
        Чат находиться в разработке 
    </div>
</aside>
<!-- SideBar Chat -->
<div class='control-sidebar-bg'></div>
