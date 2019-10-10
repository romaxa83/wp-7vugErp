<?php

use yii\db\Migration;

/**
 * Class m180307_090308_change_type_for_field_transaction
 */
class m180307_090308_change_type_for_field_transaction extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->alterColumn('operations','transaction',$this->string(50));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180307_090308_change_type_for_field_transaction cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180307_090308_change_type_for_field_transaction cannot be reverted.\n";

        return false;
    }
    */
}
