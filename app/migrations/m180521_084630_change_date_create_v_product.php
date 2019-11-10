<?php

use yii\db\Migration;

/**
 * Class m180521_084630_change_date_create_v_product
 */
class m180521_084630_change_date_create_v_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('v_product','date_create',$this->dateTime());
        $this->alterColumn('v_product','date_update',$this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180521_084630_change_date_create_v_product cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180521_084630_change_date_create_v_product cannot be reverted.\n";

        return false;
    }
    */
}
