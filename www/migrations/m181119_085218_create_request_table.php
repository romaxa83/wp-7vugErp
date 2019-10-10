<?php

use yii\db\Migration;

/**
 * Handles the creation of table `request`.
 */
class m181119_085218_create_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%request}}', [
            'id' => $this->primaryKey(),
            'comment' => $this->text(),
            'store_id' => $this->integer(),
            'status' => $this->integer(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('{{%ind_request-store_id}}','{{%request}}','store_id');

        $this->addForeignKey('{{%fk_request-store_id}}','{{%request}}','store_id','{{%agent}}','id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk_request-store_id}}','{{%request}}');

        $this->dropIndex('{{%ind_request-store_id}}','{{%request}}');

        $this->dropTable('{{%request}}');
    }
}
