<?php

use yii\db\Migration;

/**
 * Class m170929_064744_create_base_value_to_agent
 */
class m170929_064744_create_base_value_to_agent extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand()->batchInsert('agent', ['name', 'address', 'firm', 'telephone', 'data', 'price_type', 'type','status', 'is_main','created_at','updated_at'], [
            [null, null, 'Склад', null, null, 0, 3, 1, null, Yii::$app->formatter->asTimestamp(date('Y-d-m h:i:s')), Yii::$app->formatter->asTimestamp(date('Y-d-m h:i:s'))]
        ])->execute();
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m170929_064744_create_base_value_to_agent cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170929_064744_create_base_value_to_agent cannot be reverted.\n";

        return false;
    }
    */
}
