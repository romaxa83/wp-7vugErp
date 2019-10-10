<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'My Company',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => 'Контрагенты', 'url' => ['/agent/index']],
            ['label' => 'Категория', 'url' => ['/category/index']],
            ['label' => 'Товары', 'url' => ['/product/index']],
            ['label' => 'Характеристики', 'url' => ['/characteristic/index']],
            ['label' => 'Значений харкт', 'url' => ['/characteristic-value/index']],
            [
                'label' => 'Account', 
                'url' => '#', 
                'items' => [
                    ['label' => 'Set permission', 'url' => ['/account/permission']],
                    ['label' => 'Roles manage', 'url' => ['/permit/access/role']],
                    ['label' => 'Perms manage', 'url' => ['/permit/access/permission']],
                    ['label' => 'Users manage', 'url' => ['/account/index']],
                    ['label' => 'Кастомное управление ролями', 'url' => ['/permission/roles']],
                    ['label' => 'Кастомное управление разрешениями', 'url' => ['/permission/permissions']],
                ]
            ],
            ['label' => 'Home', 'url' => ['/site/index']],
            //['label' => 'About', 'url' => ['/site/about']],
            //['label' => 'Contact', 'url' => ['/site/contact']],
            [
                'label' => 'TEST', 
                'url' => '#', 
                'items' => [
                    ['label' => 'settings', 'url' => ['/account/setting']],
                ]
            ],
            Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => ['/site/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            )
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
