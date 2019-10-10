<?php

use yii\db\Migration;

/**
 * Class m180508_123504_add_amount_field_in_temp_table
 */
class m180508_123504_add_amount_field_in_temp_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('temp', 'old_amount',$this->integer(11));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('temp', 'old_amount');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180508_123504_add_amount_field_in_temp_table cannot be reverted.\n";

        return false;
    }
    */
}
