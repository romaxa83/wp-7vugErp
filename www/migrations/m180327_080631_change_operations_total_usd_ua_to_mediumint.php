<?php

use yii\db\Migration;

/**
 * Class m180327_080631_change_operations_total_usd_ua_to_mediumint
 */
class m180327_080631_change_operations_total_usd_ua_to_mediumint extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('operations','total_ua',$this->float());
        $this->alterColumn('operations','total_usd',$this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180327_080631_change_operations_total_usd_ua_to_mediumint cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }
    */
//    public function down()
//    {
//        $this->alterColumn('operations','total_ua',$this->float());
//        $this->alterColumn('operations','total_usd',$this->float());
//    }

}
