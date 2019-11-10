<?php

use yii\db\Migration;

/**
 * Class m180504_090705_drop_operation_id_in_operations
 */
class m180504_090705_drop_operation_id_in_operations extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('operations', 'operation_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180504_090705_drop_operation_id_in_operations cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180504_090705_drop_operation_id_in_operations cannot be reverted.\n";

        return false;
    }
    */
}
