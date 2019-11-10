<?php

use yii\db\Migration;

class m180904_072023_create_order_product_table extends Migration {

    public function safeUp()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('order_product', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer(),
            'product_id' => $this->integer(),
            'vproduct_id' => $this->integer(),
            'amount' => $this->integer(),
            'price' => $this->decimal(65, 13),
            'confirm' => $this->boolean(),
        ],$tableOptions);

        $this->createIndex('idx_order_product_order_id', 'order_product', 'order_id');
        $this->addForeignKey('fk_order_product_order_id', 'order_product', 'order_id', 'order', 'order', 'CASCADE');
    }

    public function safeDown() {
        $this->dropForeignKey('fk_order_product_order_id', 'order_product');
        $this->dropTable('order_product');
    }

}
