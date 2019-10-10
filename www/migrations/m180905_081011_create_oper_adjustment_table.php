<?php

use yii\db\Migration;

/**
 * Handles the creation of table `oper_adjustment`.
 */
class m180905_081011_create_oper_adjustment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('oper_adjustment', [
            'id' => $this->primaryKey(),
            'transaction_id' => $this->integer()->notNull(),
            'vproduct_id' =>$this->integer()->defaultValue(NULL),
            'product_id' => $this->integer()->notNull(),
            'amount' => $this->integer()->notNull()->defaultValue(0),
            'trade_price' => $this->decimal(24,13)->notNull(),
            'start_price' => $this->decimal(24,13)->notNull(),
            'cost_price' => $this->decimal(24,13)->notNull()
        ]);

        $this->createIndex('idx_oper_adjustment_transaction_id','oper_adjustment','transaction_id');
        $this->createIndex('idx_oper_adjustment_product_id','oper_adjustment','product_id');

        $this->addForeignKey('fk_oper_adjustment_transaction_id','oper_adjustment','transaction_id','operations','id');
        $this->addForeignKey('fk_oper_adjustment_product_id','oper_adjustment','product_id','product','id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_oper_adjustment_transaction_id', 'oper_adjustment');
        $this->dropForeignKey('fk_oper_adjustment_product_id', 'oper_adjustment');

        $this->dropIndex('idx_oper_adjustment_transaction_id', 'oper_adjustment');
        $this->dropIndex('idx_oper_adjustment_product_id', 'oper_adjustment');

        $this->dropTable('oper_adjustment');
    }
}
