<?php

use yii\db\Migration;

/**
 * Class m180625_114112_input_status_in_agent
 */
class m180625_114112_input_status_in_agent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand('UPDATE `agent` SET `status` = "1" ;')->execute();
        $this->alterColumn('agent', 'status', $this->boolean()->defaultValue(1));
        Yii::$app->db->createCommand('UPDATE `category` SET `status` = "1" ;')->execute();
        $this->alterColumn('category', 'status', $this->boolean()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180625_114112_input_status_in_agent cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180625_114112_input_status_in_agent cannot be reverted.\n";

        return false;
    }
    */
}
