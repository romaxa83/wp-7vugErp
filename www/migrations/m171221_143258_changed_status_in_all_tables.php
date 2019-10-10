<?php

use yii\db\Migration;

/**
 * Class m171221_143258_changed_status_in_all_tables
 */
class m171221_143258_changed_status_in_all_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
//        $this->alterColumn('agent','status',$this->string(4)->notNull()->defaultValue('on'));
        $this->alterColumn('category','status',$this->string(4)->notNull()->defaultValue('on'));
        $this->alterColumn('characteristic','status',$this->string(4)->notNull()->defaultValue('on'));
        $this->alterColumn('product','status',$this->string(4)->notNull()->defaultValue('on'));
//        $this->alterColumn('user','status',$this->string(4)->notNull()->defaultValue('on'));
        $this->alterColumn('variant_product','status',$this->string(4)->notNull()->defaultValue('on'));

        echo 'EVERYWHERE THE STATUS IS CHANGED';
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m171221_143258_changed_status_in_all_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171221_143258_changed_status_in_all_tables cannot be reverted.\n";

        return false;
    }
    */
}
