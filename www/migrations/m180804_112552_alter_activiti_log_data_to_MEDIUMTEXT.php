<?php

use yii\db\Migration;

/**
 * Class m180804_112552_alter_activiti_log_data_to_MEDIUMTEXT
 */
class m180804_112552_alter_activiti_log_data_to_MEDIUMTEXT extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->createCommand("ALTER TABLE activity_log MODIFY data MEDIUMTEXT")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180804_112552_alter_activiti_log_data_to_MEDIUMTEXT cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180804_112552_alter_activiti_log_data_to_MEDIUMTEXT cannot be reverted.\n";

        return false;
    }
    */
}
