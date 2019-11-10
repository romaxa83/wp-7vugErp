<?php

use yii\db\Migration;

/**
 * Class m180622_105132_add_vendore_code_in_v_product
 */
class m180622_105132_add_vendore_code_in_v_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('v_product', 'vendor_code',$this->string(30));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('v_product', 'vendor_code');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180622_105132_add_vendore_code_in_v_product cannot be reverted.\n";

        return false;
    }
    */
}
