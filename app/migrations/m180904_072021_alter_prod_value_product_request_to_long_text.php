<?php

use yii\db\Migration;

/**
 * Class m180904_072021_alter_prod_value_product_request_to_long_text
 */
class m180904_072021_alter_prod_value_product_request_to_long_text extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('product_request', 'prod_value', $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180904_072021_alter_prod_value_product_request_to_long_text cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180904_072021_alter_prod_value_product_request_to_long_text cannot be reverted.\n";

        return false;
    }
    */
}
