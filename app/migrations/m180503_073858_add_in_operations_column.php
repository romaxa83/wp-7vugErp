<?php

use yii\db\Migration;

/**
 * Class m180503_073858_add_in_operations_column
 */
class m180503_073858_add_in_operations_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('operations', 'course',$this->double()->defaultValue(26.10));
        $this->addColumn('operations', 'trade_price',$this->double());
        $this->addColumn('operations', 'start_price',$this->double());
        $this->addColumn('operations', 'cost_price',$this->double());
        $this->addColumn('operations', 'date_update',$this->text());
        $this->addColumn('operations', 'recalculated',$this->integer(2));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('operations', 'course');
        $this->dropColumn('operations', 'trade_price');
        $this->dropColumn('operations', 'start_price');
        $this->dropColumn('operations', 'cost_price');
        $this->dropColumn('operations', 'date_update');
        $this->dropColumn('operations', 'recalculated');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180503_073858_add_in_operations_column cannot be reverted.\n";

        return false;
    }
    */
}
