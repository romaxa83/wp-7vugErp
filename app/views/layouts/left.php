<?php

use dmstr\widgets\Menu;
?>
<aside class="main-sidebar">
    <section class="sidebar">
        <?php  
            echo Menu::widget([
                'options' => ['class' => 'sidebar-menu'],
                'items' => [
                    ['label' => 'Товары', 'icon' => 'list-alt', 'url' => ['/product/index'] , 'visible' => (Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'product_create') || Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'product_update'))],
                    [
                        'label' => 'Документы',
                        'icon' => 'folder-o',
                        'url' => '/operations/index',
                        'items' => [
                            ['label' => 'Все транзакции', 'icon' => 'circle-o', 'url' => ['/operation/all-transaction']],
                            ['label' => 'Приход', 'icon' => 'circle-o', 'url' => ['/operation/all-coming']],
                            ['label' => 'Расход', 'icon' => 'circle-o', 'url' => ['/operation/all-consumption']],
                            ['label' => 'Заявки', 'icon' => 'circle-o', 'url' => ['/manager/admin/index']],
                            ['label' => 'Архив', 'icon' => 'circle-o', 'url' => ['/operation/archive']],
                        ],
                        'visible' => (Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'operation_create') || Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'operation_print') || Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'operation_update'))
                    ],
                    ['label' => 'Каталог', 'icon' => 'bar-chart', 'url' => '#',
                        'items' => [
                            ['label' => 'Категории', 'icon' => 'circle-o', 'url' => ['/category/index'], 'visible' => (Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'category_create') || Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'category_update'))],
                            ['label' => 'Характеристики', 'icon' => 'circle-o', 'url' => ['/characteristic/index'], 'visible' => (Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'characteristic_create') || Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'characteristic_update'))],
                            ['label' => 'Магазины', 'icon' => 'circle-o', 'url' => ['/agent/store'], 'visible' => (Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'agent_create') || Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'agent_update'))],
                            ['label' => 'Поставщики', 'icon' => 'circle-o', 'url' => ['/agent/provider'], 'visible' => (Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'agent_create') || Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'agent_update'))],
                        ],
                        'visible' => !Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'manager')
                    ],
                    ['label' => 'Добавление', 'icon' => 'plus', 'url' => '#',
                        'items' => [
                            ['label' => 'Добавить товар' ,'icon' => 'circle-o', 'url' => ['/product/create'], 'visible' => Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'product_create')],
                            ['label' => 'Добавить категорию' ,'icon' => 'circle-o', 'url' => ['/category/create'], 'visible' => Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'category_create')],
                            ['label' => 'Добавить контрагента' ,'icon' => 'circle-o', 'url' => ['/agent/create'], 'visible' => Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'agent_create')],
                        ],
                        'visible' => !Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'manager')
                    ],
                    [
                        'label' => 'Отчеты',
                        'icon' => 'file-text-o',
                        'url' => '#',
                        'class' => 'hidden',
                        'items' => [
                            ['label' => 'Движение товара', 'icon' => 'circle-o', 'url' => ['/trafic-product/index']],
                            ['label' => 'Отчет', 'icon' => 'circle-o', 'url' => ['/chart/index']]
                        ],
                        'visible' => Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'admin')
                    ],
                    [
                        'label' => 'Администрирование',
                        'icon' => 'paper-plane-o',
                        'url' => '/account/index',
                        'items' => [
                            ['label' => 'Пользователи и роли', 'icon' => 'circle-o', 'url' => ['/user/index']],
                            ['label' => 'Управление доступом', 'url' => ['/access'], 'visible' => Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'admin')],
                            ['label' => 'Журнал активности', 'url' => ['/logger/default/index'], 'visible' => Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'admin')]
                        ],
                        'visible' => (Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'admin') || Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'user_add_view'))
                    ], 
                    ['label' => 'Выход', 'url' => ['/site/logout'], 'visible' => !Yii::$app->user->isGuest && !Yii::$app->authManager->checkAccess(Yii::$app->user->id, 'manager')]
                ]
            ]);
        ?>
    </section>
</aside>
