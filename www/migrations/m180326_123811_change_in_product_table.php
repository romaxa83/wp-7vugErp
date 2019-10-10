<?php

use yii\db\Migration;

/**
 * Class m180326_123811_change_in_product_table
 */
class m180326_123811_change_in_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
//        $this->alterColumn('product','start_price',$this->decimal(11,10));
//        $this->alterColumn('product','cost_price',$this->decimal(11,10));
//        $this->alterColumn('product','trade_price',$this->decimal(11,10));
//        $this->alterColumn('product','price1',$this->decimal(11,10));
//        $this->alterColumn('product','price2',$this->decimal(11,10));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180326_123811_change_in_product_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180326_123811_change_in_product_table cannot be reverted.\n";

        return false;
    }
    */
}
