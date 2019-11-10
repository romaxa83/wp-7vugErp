<?php

use yii\db\Migration;

/**
 * Class m181120_094438_add_total_price_archive
 */
class m181120_094438_add_total_price_archive extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('archive', 'trade_price',$this->decimal(24,13)->defaultValue(null));
        $this->addColumn('archive', 'cost_price',$this->decimal(24,13)->defaultValue(null));
        $this->addColumn('archive', 'start_price',$this->decimal(24,13)->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181120_094438_add_total_price_archive cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181120_094438_add_total_price_archive cannot be reverted.\n";

        return false;
    }
    */
}
