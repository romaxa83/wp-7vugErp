<?php

use yii\db\Migration;

/**
 * Class m180627_154136_change_status_product
 */
class m180627_154136_change_status_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand('UPDATE `product` SET `status` = "1" ;')->execute();
        $this->alterColumn('product', 'status', $this->boolean()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180627_154136_change_status_product cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180627_154136_change_status_product cannot be reverted.\n";

        return false;
    }
    */
}
