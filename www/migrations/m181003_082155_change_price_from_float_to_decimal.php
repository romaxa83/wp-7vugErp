<?php

use yii\db\Migration;

/**
 * Class m181003_082155_change_price_from_float_to_decimal
 */
class m181003_082155_change_price_from_float_to_decimal extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('product', 'start_price', $this->decimal(24,13));
        $this->alterColumn('product', 'trade_price', $this->decimal(24,13));
        $this->alterColumn('product', 'cost_price', $this->decimal(24,13));
        $this->alterColumn('product', 'price1', $this->decimal(24,13));
        $this->alterColumn('product', 'price2', $this->decimal(24,13));
        $this->alterColumn('operations', 'start_price', $this->decimal(24,13));
        $this->alterColumn('operations', 'trade_price', $this->decimal(24,13));
        $this->alterColumn('operations', 'cost_price', $this->decimal(24,13));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181003_082155_change_price_from_float_to_decimal cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181003_082155_change_price_from_float_to_decimal cannot be reverted.\n";

        return false;
    }
    */
}
