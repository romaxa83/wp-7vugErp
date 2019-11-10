<?php

use yii\db\Migration;

/**
 * Class m180228_123455_add_column_in_operation
 */
class m180228_123455_add_column_in_operation extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('operations', 'total_usd',$this->decimal(11,3));
        $this->addColumn('operations', 'total_ua',$this->decimal(11,3));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('operations', 'total_usd');
        $this->dropColumn('operations', 'total_ua');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180228_123455_add_column_in_operation cannot be reverted.\n";

        return false;
    }
    */
}
