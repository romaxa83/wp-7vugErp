<?php

use yii\db\Migration;

/**
 * Class m180327_082456_change_product
 */
class m180327_082456_change_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('product','start_price',$this->float());
        $this->alterColumn('product','cost_price',$this->float());
        $this->alterColumn('product','trade_price',$this->float());
        $this->alterColumn('product','price1',$this->float());
        $this->alterColumn('product','price2',$this->float());
    }

    /**
     * {@inheritdoc}
     */
//    public function safeDown()
//    {
//        $this->alterColumn('product','start_price',$this->float());
//        $this->alterColumn('product','cost_price',$this->float());
//        $this->alterColumn('product','trade_price',$this->float());
//        $this->alterColumn('product','price1',$this->float());
//        $this->alterColumn('product','price2',$this->float());
//    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180327_082456_change_product cannot be reverted.\n";

        return false;
    }
    */
}
