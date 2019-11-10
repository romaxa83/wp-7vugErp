<?php

use yii\db\Migration;

/**
 * Class m180609_093906_add_date_adjustment_in_product
 */
class m180609_093906_add_date_adjustment_in_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('product', 'date_adjustment',$this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('product', 'date_adjustment');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180609_093906_add_date_adjustment_in_product cannot be reverted.\n";

        return false;
    }
    */
}
