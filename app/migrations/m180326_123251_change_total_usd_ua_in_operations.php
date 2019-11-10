<?php

use yii\db\Migration;

/**
 * Class m180326_123251_change_total_usd_ua_in_operations
 */
class m180326_123251_change_total_usd_ua_in_operations extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
//        $this->alterColumn('operations','total_ua',$this->decimal(11,10));
//        $this->alterColumn('operations','total_usd',$this->decimal(11,10));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180326_123251_change_total_usd_ua_in_operations cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180326_123251_change_total_usd_ua_in_operations cannot be reverted.\n";

        return false;
    }
    */
}
