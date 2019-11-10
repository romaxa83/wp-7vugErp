<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */
?>
<div class="yiierp-loader">
    <div class="circ1"></div>
    <div class="circ2"></div>
    <div class="circ3"></div>
</div>
<header class="main-header">
    <nav class="navbar navbar-static-top" role="navigation">
        <div class="navbar-custom-menu  navbar-left">
            <ul class="nav navbar-nav">
                <li>
                    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                        <span class="sr-only">Toggle navigation</span>
                    </a>
                </li>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'product_create') || Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'product_update')) : ?>
                    <li class="nav-link left-link">
                        <?= Html::a('<span class="hell">Каталог товаров</span> <i class="fa fa-list-alt" aria-hidden="true"></i>',Url::toRoute(['/product/index']),['class' => 'fast-link-catalog btn btn-success snap'])?>
                    </li>
                <?php endif; ?>
                <?php if(Yii::$app->user->can('manager')) : ?>
                    <li class="nav-link left-link">
                        <?= 
                            Html::a('<span class="hell">Каталог товаров</span> <i class="fa fa-list-alt" aria-hidden="true"></i>',Url::toRoute(['/manager/manager/index']),['class' => 'fast-link-catalog btn btn-success snap'])
                        ?>
                    </li>
                    <li class="nav-link left-link">
                        <?= 
                            Html::a('<span class="hell">Таблица товаров</span> <i class="fa fa-eye" aria-hidden="true"></i>',
                                Url::toRoute(['/manager/manager/index']),
                                [
                                    'class' => 'fast-link-catalog btn btn-success snap collapse-manager-product',
                                    'data-target' => '.product-for-manager',
                                    'data-toggle' => 'collapse'
                                ]
                            );
                        ?>
                    </li>
                <?php endif; ?>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'operation_create')) : ?>
                    <li class="nav-link left-link">
                        <?= Html::a('Приход товара',Url::toRoute(['/operation-coming/create']))?>
                    </li>

                     <li class="nav-link left-link">
                        <?= Html::a('Расход товара',Url::toRoute(['/operation-consumption/create']))?>
                    </li>
                    <li class="nav-link left-link">
                        <?= Html::a('Корректировка товара',Url::toRoute(['/operation-adjustment/index']))?>
                    </li>
                <?php endif; ?>
                <?php if(Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'admin')) : ?>
                    <span class="<?= Yii::$app->controller->module->id == 'manager' ? 'hidden' : 'environmentPoint' ?>"> 
                        <?= Yii::$app->params['environmentPoint'] === '{ENVIRONMENT}' ? 'local version' : Yii::$app->params['environmentPoint'] ?> 
                    </span>
                <?php endif; ?>
                <li class="nav-currency nav-shift">
                    <span class="date"><?= date('d.m.Y');?></span>
                    <a href="#" data-toggle="dropdown" class="btn-currency dropdown-toggle"><i class="fa fa-usd"></i></a>
                    <?php if (Yii::$app->user->can('settings')):?>
                        <ul class="dropdown-menu">
                            <li class="currency_drop">
                                <div class="pull-left">
                                    <input class="currency_input" type="float" value="<?php echo getUsd()?>" name="currency_value" size="5">
                                </div>
                                <div class="pull-right">
                                    <button class="save_currency btn btn-default btn-flat">Save <i class="fa fa-usd"></i></button>
                                </div>
                            </li>
                        </ul>
                    <?php endif;?>
                    <span class="curr-value"><?php echo getUsd()?></span>
                </li>
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                       <!--  <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="user-image" alt="User Image"/> -->
                        <i class="fa fa-user"></i>
                        <span class="hidden-md hidden-xs"><?= Yii::$app->user->getIdentity()->username?>(<?= Yii::$app->user->getIdentity()->role?>)</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="user-header">
                            <p><?= Yii::$app->user->getIdentity()->username?>(<?= Yii::$app->user->getIdentity()->role?>)</p>
                        </li>
                        <li class="user-footer">
                            <button id="show-chat" type="button" class="btn btn-success">
                                Чат
                            </button>
                            <div class="pull-right">
                                <?= Html::a('Выйти',\yii\helpers\Url::toRoute('/site/logout'),['class' => 'btn btn-default btn-flat btn-logout','data-method' => 'post'])?>
                            </div>
                        </li>
                    </ul>
                </li>
                <?php if (Yii::$app->user->can('settings')):?>
                    <li class="nav-link">
                        <?= Html::a(''. '<i class="fa fa-cog"></i>' .'',['/site/settings']);?>
                    </li>
                <?php endif;?>
                <li class="nav-link">
                    <?= Html::a(''. '<i class="fa fa-sign-out"></i>' .'',\yii\helpers\Url::toRoute('/site/logout'),['data-method' => 'post']);?>
                </li>
            </ul>
        </div>
    </nav>
</header>
