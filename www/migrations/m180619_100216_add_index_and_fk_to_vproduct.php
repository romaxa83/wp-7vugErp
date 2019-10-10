<?php

use yii\db\Migration;

/**
 * Class m180619_100216_add_index_and_fk_to_vproduct
 */
class m180619_100216_add_index_and_fk_to_vproduct extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createIndex('fk_v_product_id','v_product','product_id');

        $this->addForeignKey('fk_v_product_id','v_product','product_id','product','id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_v_product_id', 'v_product');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180619_100216_add_index_and_fk_to_vproduct cannot be reverted.\n";

        return false;
    }
    */
}
