<?php

use yii\db\Migration;

/**
 * Class m181009_083508_create_table_connect_many_to_many_product_with_agent
 */
class m181009_083508_create_table_connect_many_to_many_product_with_agent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('product_agent', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer(),
            'agent_id' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('product_agent');
    }
}
