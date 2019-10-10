<?php

use yii\db\Migration;

/**
 * Class m180829_065340_alert_column_vendor_code_to_bigint
 */
class m180829_065340_alert_column_vendor_code_to_bigint extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('product', 'vendor_code', $this->bigInteger());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180829_065340_alert_column_vendor_code_to_bigint cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180829_065340_alert_column_vendor_code_to_bigint cannot be reverted.\n";

        return false;
    }
    */
}
