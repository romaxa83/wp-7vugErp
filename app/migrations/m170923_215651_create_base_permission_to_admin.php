<?php

use yii\db\Migration;
use app\models\User;

class m170923_215651_create_base_permission_to_admin extends Migration {

    public function safeUp() {

        $auth = \Yii::$app->authManager;

        $auth->removeAll(); //На всякий случай удаляем старые данные из БД...
        // Создадим роль админа
        $admin = $auth->createRole('admin');
        $admin->description = 'Администратор';

        $storekeeper = $auth->createRole('storekeeper');
        $storekeeper->description = 'Кладовшик';

        $manager = $auth->createRole('manager');
        $manager->description = 'Менеджер';

        // запишем разрешения в БД
        $auth->add($admin);
        $auth->add($storekeeper);
        $auth->add($manager);

        $user_add_view = $auth->createPermission('user_add_view');
        $user_add_view->description = 'Добавление и просмотр пользователей"';

        $settings = $auth->createPermission('settings');
        $settings->description = 'Доступ к настройкам';

        $agent_create = $auth->createPermission('agent_create');
        $agent_create->description = 'Создание контрагента/магазина/склада';

        $agent_delete = $auth->createPermission('agent_delete');
        $agent_delete->description = 'Удаление контрагента/магазина/склада';

        $agent_update = $auth->createPermission('agent_update');
        $agent_update->description = 'Редактирование контрагента/магазина/склада';

        $category_create = $auth->createPermission('category_create');
        $category_create->description = 'Создание категорий';

        $category_delete = $auth->createPermission('category_delete');
        $category_delete->description = 'Удаление категорий';

        $category_update = $auth->createPermission('category_update');
        $category_update->description = 'Редактирование категорий';

        $characteristic_create = $auth->createPermission('characteristic_create');
        $characteristic_create->description = 'Создание характеристик';

        $characteristic_delete = $auth->createPermission('characteristic_delete');
        $characteristic_delete->description = 'Удаление характеристик';

        $characteristic_update = $auth->createPermission('characteristic_update');
        $characteristic_update->description = 'Редактирование характеристик';

        $operation_create = $auth->createPermission('operation_create');
        $operation_create->description = 'Создание транзакций';

        $operation_delete = $auth->createPermission('operation_delete');
        $operation_delete->description = 'Удаление транзакций';

        $operation_update = $auth->createPermission('operation_update');
        $operation_update->description = 'Редактирование транзакций';

        $operation_print = $auth->createPermission('operation_print');
        $operation_print->description = 'Распечатка транзакций';

        $product_create = $auth->createPermission('product_create');
        $product_create->description = 'Создание товара';

        $product_delete = $auth->createPermission('product_delete');
        $product_delete->description = 'Удаление товара';

        $product_update = $auth->createPermission('product_update');
        $product_update->description = 'Редактирование товара';

        // Запишем эти разрешения в БД
        $auth->add($user_add_view);
        $auth->add($settings);
        $auth->add($agent_create);
        $auth->add($agent_delete);
        $auth->add($agent_update);
        $auth->add($category_create);
        $auth->add($category_delete);
        $auth->add($category_update);
        $auth->add($characteristic_create);
        $auth->add($characteristic_delete);
        $auth->add($characteristic_update);
        $auth->add($operation_create);
        $auth->add($operation_delete);
        $auth->add($operation_update);
        $auth->add($operation_print);
        $auth->add($product_create);
        $auth->add($product_delete);
        $auth->add($product_update);

        $auth->addChild($admin, $user_add_view);
        $auth->addChild($admin, $settings);
        $auth->addChild($admin, $agent_create);
        $auth->addChild($admin, $agent_delete);
        $auth->addChild($admin, $agent_update);
        $auth->addChild($admin, $category_create);
        $auth->addChild($admin, $category_delete);
        $auth->addChild($admin, $category_update);
        $auth->addChild($admin, $characteristic_create);
        $auth->addChild($admin, $characteristic_delete);
        $auth->addChild($admin, $characteristic_update);
        $auth->addChild($admin, $operation_create);
        $auth->addChild($admin, $operation_delete);
        $auth->addChild($admin, $operation_update);
        $auth->addChild($admin, $operation_print);
        $auth->addChild($admin, $product_create);
        $auth->addChild($admin, $product_delete);
        $auth->addChild($admin, $product_update);

        $auth->addChild($storekeeper, $user_add_view);
        $auth->addChild($storekeeper, $settings);
        $auth->addChild($storekeeper, $agent_create);
        $auth->addChild($storekeeper, $agent_delete);
        $auth->addChild($storekeeper, $agent_update);
        $auth->addChild($storekeeper, $category_create);
        $auth->addChild($storekeeper, $category_delete);
        $auth->addChild($storekeeper, $category_update);
        $auth->addChild($storekeeper, $characteristic_create);
        $auth->addChild($storekeeper, $characteristic_delete);
        $auth->addChild($storekeeper, $characteristic_update);
        $auth->addChild($storekeeper, $operation_create);
        $auth->addChild($storekeeper, $operation_delete);
        $auth->addChild($storekeeper, $operation_update);
        $auth->addChild($storekeeper, $operation_print);
        $auth->addChild($storekeeper, $product_create);
        $auth->addChild($storekeeper, $product_delete);
        $auth->addChild($storekeeper, $product_update);

        $user = new User();
        $user->username = 'admin';
        $user->email = 'admin@admin.com';
        $user->setPassword('admin');
        $user->password = 'admin';
        $user->role = 'admin';
        $user->generateAuthKey();

        if ($user->save()) {
            //$auth->assign($admin, $user->id);
            echo 'OK all permission added';
        } else {
            echo 'ERROR';
        }
    }

    public function safeDown() {
        echo 'Can not be reverted !!';
    }

}
