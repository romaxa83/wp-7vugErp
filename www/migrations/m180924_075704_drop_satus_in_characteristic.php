<?php

use yii\db\Migration;

/**
 * Class m180924_075704_drop_satus_in_characteristic
 */
class m180924_075704_drop_satus_in_characteristic extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('characteristic', 'status');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('characteristic', 'status',$this->string(4)->defaultValue('on'));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180924_075704_drop_satus_in_characteristic cannot be reverted.\n";

        return false;
    }
    */
}
