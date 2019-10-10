<?php

use yii\db\Migration;

/**
 * Handles the creation of table `archive`.
 */
class m180907_095625_create_archive_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('archive', [
            'id' => $this->primaryKey(),
            'type' => $this->integer(1),
            'transaction_id' => $this->integer(11),
            'transaction' => $this->string(50),
            'whence' => $this->integer(11),
            'where' => $this->integer(11),
            'total_usd' => $this->decimal(24,13),
            'total_ua' => $this->decimal(24,13),
            'date' => $this->dateTime(),
            'date_archive' => $this->dateTime(),
        ]);

        $this->createIndex('idx_archive_whence','archive','whence');
        $this->createIndex('idx_archive_where','archive','where');

        $this->addForeignKey('fk_archive_whence','archive','whence','agent','id');
        $this->addForeignKey('fk_archive_where','archive','where','agent','id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_archive_whence', 'archive');
        $this->dropForeignKey('fk_archive_where', 'archive');

        $this->dropIndex('idx_archive_whence', 'archive');
        $this->dropIndex('idx_archive_where', 'archive');

        $this->dropTable('archive');
    }
}
