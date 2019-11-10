<?php
return yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/test.php',
    require __DIR__ . '/test-console.php',
//    require __DIR__ . '/web.php',
//    require __DIR__ . '/web-local.php',
    [
    ]
);