<?php

use yii\db\Migration;

/**
 * Handles the creation of table `archive_value`.
 */
class m180907_103212_create_archive_value_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('archive_value', [
            'id' => $this->primaryKey(),
            'archive_id' => $this->integer()->notNull(),
            'vproduct_id' =>$this->integer()->defaultValue(NULL),
            'product_id' => $this->integer()->notNull(),
            'amount' => $this->integer(),
            'price' => $this->decimal(24,13),
            'price1' => $this->decimal(24,13),
            'price2' => $this->decimal(24,13),
            'trade_price' => $this->decimal(24,13),
            'start_price' => $this->decimal(24,13),
            'cost_price' => $this->decimal(24,13)
        ],$tableOptions);

        $this->createIndex('idx_archive_value_archive_id','archive_value','archive_id');
        $this->addForeignKey('fk_archive_value_archive_id','archive_value','archive_id','archive','id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_archive_value_archive_id', 'archive_value');
        $this->dropIndex('idx_archive_value_archive_id', 'archive_value');
        $this->dropTable('archive_value');
    }
}
