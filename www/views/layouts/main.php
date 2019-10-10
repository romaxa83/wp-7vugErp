<?php

use dmstr\widgets\Alert;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */


// if (Yii::$app->controller->action->id === 'login') { 
/**
 * Do not use this code in your template. Remove it. 
 * Instead, use the code  $this->layout = '//main-login'; in your controller.
 */
//     echo $this->render(
//         'main-login',
//         ['content' => $content]
//     );
// } else {
    dmstr\web\AdminLteAsset::register($this);

    if (class_exists('backend\assets\AppAsset')) {
        backend\assets\AppAsset::register($this);
    } else {
        app\assets\AppAsset::register($this);
    }

    

    $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
    ?>
    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body class="hold-transition skin-blue sidebar-mini <?= ((isset($_COOKIE['menu']) && $_COOKIE['menu'] == 'collapse') || Yii::$app->user->identity->role == 'manager') ? 'sidebar-collapse' : '' ?>" data-warning='<?php echo (isset($_SESSION['warning']))? $_SESSION['warning']:FALSE;?>'>
    <?php $this->beginBody() ?>
    <div class="wrapper">

         <?= $this->render(
            'header.php',
            ['directoryAsset' => $directoryAsset]
        ) ?>

        <?= $this->render(
            'left.php',
            ['directoryAsset' => $directoryAsset]
        )
        ?>
        <div id="focus-wrapper">
            <?php echo Alert::widget() ?>
        <?= $this->render(
            'content.php',
            ['content' => $content, 'directoryAsset' => $directoryAsset]
        ) ?>
        </div>
        <div class="noty_layout"></div>
    </div>

    <?php $this->endBody() ?>

    </body>
    </html>
    <?php $this->endPage() ?>
<?php //} ?>
