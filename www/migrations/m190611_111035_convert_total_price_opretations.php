<?php

use yii\db\Migration;

/**
 * Class m190611_111035_test
 */
class m190611_111035_convert_total_price_opretations extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('operations', 'total_ua', $this->decimal(24,13));
        $this->alterColumn('operations', 'total_usd', $this->decimal(24,13));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('operations', 'total_ua', $this->decimal(24,10));
        $this->alterColumn('operations', 'total_usd', $this->decimal(24,10));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190611_111035_test cannot be reverted.\n";

        return false;
    }
    */
}
