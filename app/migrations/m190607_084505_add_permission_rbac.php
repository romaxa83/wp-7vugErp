<?php

use yii\db\Migration;

/**
 * Class m190607_084505_add_permission_rbac
 */
class m190607_084505_add_permission_rbac extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;
        $product_print = $auth->createPermission('product_print');
        $product_print->description = 'печать товаров';
        $auth->add($product_print);
        $role = Yii::$app->authManager->getRole('admin');
        $permission = Yii::$app->authManager->getPermission('product_print');
        Yii::$app->authManager->addChild($role, $permission);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $role = Yii::$app->authManager->getRole('admin');
        $permission = Yii::$app->authManager->getPermission('product_print');
        Yii::$app->authManager->removeChild($role, $permission);
        Yii::$app->authManager->remove($permission);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190607_084505_add_permission_rbac cannot be reverted.\n";

        return false;
    }
    */
}
