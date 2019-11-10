<?php

use yii\db\Migration;

/**
 * Class m180214_072526_add_columns_in_product_table
 */
class m180214_072526_add_columns_in_product_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('product', 'min_amount', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('product', 'margin', $this->integer()->notNull()->defaultValue(20));
        $this->addColumn('variant_product', 'min_amount', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('variant_product', 'margin', $this->integer()->notNull()->defaultValue(20));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('product', 'min_amount');
        $this->dropColumn('product', 'margin');
        $this->dropColumn('variant_product', 'min_amount');
        $this->dropColumn('variant_product', 'margin');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180214_072526_add_columns_in_product_table cannot be reverted.\n";

        return false;
    }
    */
}
