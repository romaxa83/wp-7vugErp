<?php

use yii\db\Migration;

/**
 * Class m180612_074531_convert_date_in_archive
 */
class m180612_074531_convert_date_in_archive extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand('UPDATE `oper_archive` SET `date` = str_to_date(`date`, "%d.%m.%Y %H:%i:%s" ) WHERE date LIKE "%.%" ;')->execute();
        Yii::$app->db->createCommand('ALTER TABLE `oper_archive`  MODIFY COLUMN `date` DATETIME;')->execute();
        Yii::$app->db->createCommand('UPDATE `oper_archive` SET `date_archive` = str_to_date(`date_archive`, "%d.%m.%Y %H:%i:%s" ) WHERE date_archive LIKE "%.%" ;')->execute();
        Yii::$app->db->createCommand('ALTER TABLE `oper_archive`  MODIFY COLUMN `date_archive` DATETIME;')->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180612_074531_convert_date_in_archive cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180612_074531_convert_date_in_archive cannot be reverted.\n";

        return false;
    }
    */
}
