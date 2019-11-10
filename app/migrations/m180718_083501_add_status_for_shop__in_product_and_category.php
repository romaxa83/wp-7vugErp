<?php

use yii\db\Migration;

/**
 * Class m180718_074359_add_status_for_shop__in_product
 */
class m180718_083501_add_status_for_shop__in_product_and_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('product', 'publish_status',$this->boolean()->defaultValue(null));
        $this->addColumn('category', 'publish_status',$this->boolean()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('product', 'publish_status');
        $this->dropColumn('category', 'publish_status');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180718_074359_add_status_for_shop__in_product cannot be reverted.\n";

        return false;
    }
    */
}
