<?php

use yii\db\Migration;

/**
 * Class m180625_065405_added_change_price_to_vproduct
 */
class m180625_065405_added_change_price_to_vproduct extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('v_product', 'change_price', $this->integer()->notNull()->defaultValue(0));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('v_product','change_price');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180625_065405_added_change_price_to_vproduct cannot be reverted.\n";

        return false;
    }
    */
}
