<?php

use yii\db\Migration;

/**
 * Class m180531_125205_convert_date_and_date_update_in_operations
 */
class m180531_125205_convert_date_and_date_update_in_operations extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand('UPDATE `operations` SET `date` = str_to_date(`date`, "%d.%m.%Y %H:%i:%s" );')->execute();
        Yii::$app->db->createCommand('ALTER TABLE `operations`  MODIFY COLUMN `date` DATETIME;')->execute();
        Yii::$app->db->createCommand('UPDATE `operations` SET `date_update` = str_to_date(`date_update`, "%d.%m.%Y %H:%i:%s" );')->execute();
        Yii::$app->db->createCommand('ALTER TABLE `operations`  MODIFY COLUMN `date_update` DATETIME;')->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180531_125205_convert_date_and_date_update_in_operations cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180531_125205_convert_date_and_date_update_in_operations cannot be reverted.\n";

        return false;
    }
    */
}
