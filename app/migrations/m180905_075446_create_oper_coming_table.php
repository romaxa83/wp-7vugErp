<?php

use yii\db\Migration;

/**
 * Handles the creation of table `oper_coming`.
 */
class m180905_075446_create_oper_coming_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('oper_coming', [
            'id' => $this->primaryKey(),
            'transaction_id' => $this->integer()->notNull(),
            'vproduct_id' =>$this->integer()->defaultValue(NULL),
            'product_id' => $this->integer()->notNull(),
            'amount' => $this->integer()->notNull()->defaultValue(0),
            'price1' => $this->decimal(24,13)->notNull(),
            'price2' => $this->decimal(24,13)->notNull(),
            'start_price' => $this->decimal(24,13)->notNull(),
            'cost_price' => $this->decimal(24,13)->notNull(),
            'old_amount' => $this->integer()->notNull()->defaultValue(0),
            'old_cost_price' => $this->decimal(24,13)->notNull()

        ],$tableOptions);

        $this->createIndex('idx_oper_coming_transaction_id','oper_coming','transaction_id');
        $this->addForeignKey('fk_oper_coming_transaction_id','oper_coming','transaction_id','operations','id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_oper_coming_transaction_id', 'oper_coming');
        $this->dropIndex('idx_oper_coming_transaction_id', 'oper_coming');
        $this->dropTable('oper_coming');
    }
}
