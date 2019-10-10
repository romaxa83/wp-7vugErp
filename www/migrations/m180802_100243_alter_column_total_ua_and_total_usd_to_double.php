<?php

use yii\db\Migration;

/**
 * Class m180802_100243_alter_column_total_ua_and_total_usd_to_double
 */
class m180802_100243_alter_column_total_ua_and_total_usd_to_double extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('operations', 'total_ua', $this->decimal(24,10));
        $this->alterColumn('operations', 'total_usd', $this->decimal(24,10));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180802_100243_alter_column_total_ua_and_total_usd_to_double cannot be reverted.\n";

        return false;
    }
    */
}
