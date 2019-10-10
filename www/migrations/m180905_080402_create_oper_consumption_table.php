<?php

use yii\db\Migration;

/**
 * Handles the creation of table `oper_consumption`.
 */
class m180905_080402_create_oper_consumption_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('oper_consumption', [
            'id' => $this->primaryKey(),
            'transaction_id' => $this->integer()->notNull(),
            'vproduct_id' =>$this->integer()->defaultValue(NULL),
            'product_id' => $this->integer()->notNull(),
            'amount' => $this->integer()->notNull()->defaultValue(0),
            'price' => $this->decimal(24,13)->notNull(),
            'trade_price' => $this->decimal(24,13)->notNull(),
            'cost_price' => $this->decimal(24,13)->notNull()
        ]);

        $this->createIndex('idx_oper_consumption_transaction_id','oper_consumption','transaction_id');
        $this->addForeignKey('fk_oper_consumption_transaction_id','oper_consumption','transaction_id','operations','id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_oper_consumption_transaction_id', 'oper_consumption');
        $this->dropIndex('idx_oper_consumption_transaction_id', 'oper_consumption');
        $this->dropTable('oper_consumption');
    }
}
