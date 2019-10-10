<?php

use yii\db\Migration;

/**
 * Class m180115_071608_add_column_change_price_in_tables
 */
class m180115_071608_add_column_change_price_in_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->addColumn('product', 'change_price', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('variant_product', 'change_price', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('settings', 'mes_change_price', $this->integer()->notNull()->defaultValue(0));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('product', 'change_price');
        $this->dropColumn('variant_product', 'change_price');
        $this->dropColumn('settings', 'mes_change_price');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180115_071608_add_column_change_price_in_tables cannot be reverted.\n";

        return false;
    }
    */
}
