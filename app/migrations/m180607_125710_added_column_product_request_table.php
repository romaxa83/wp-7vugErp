<?php

use yii\db\Migration;

/**
 * Class m180607_125710_added_column_product_request_table
 */
class m180607_125710_added_column_product_request_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('product_request', 'transaction_id',$this->integer(11));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('product_request', 'transaction_id');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180607_125710_added_column_product_request_table cannot be reverted.\n";

        return false;
    }
    */
}
