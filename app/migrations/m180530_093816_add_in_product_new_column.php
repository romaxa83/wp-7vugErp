<?php

use yii\db\Migration;

/**
 * Class m180530_093816_add_in_product_new_column
 */
class m180530_093816_add_in_product_new_column extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('product', 'view_manager',$this->integer(1)->notNull()->defaultValue(0));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('product','view_manager');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180530_093816_add_in_product_new_column cannot be reverted.\n";

        return false;
    }
    */
}
