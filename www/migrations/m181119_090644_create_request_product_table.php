<?php

use yii\db\Migration;

/**
 * Handles the creation of table `request_product`.
 */
class m181119_090644_create_request_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%request_product}}', [
            'id' => $this->primaryKey(),
            'request_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'vproduct_id' => $this->integer(),
            'amount' => $this->integer()->notNull()->defaultValue(0),
            'price' => $this->decimal(24,13)->notNull(),
            'cost_price' => $this->decimal(24,13)->notNull(),
            'trade_price' => $this->decimal(24,13)->notNull(),
        ]);

        $this->createIndex('{{%ind_request_product-request_id}}','{{%request_product}}','request_id');
        $this->createIndex('{{%ind_request_product-product_id}}','{{%request_product}}','product_id');
        $this->createIndex('{{%ind_request_product-vproduct_id}}','{{%request_product}}','vproduct_id');

        $this->addForeignKey('{{%fk_request_product-request_id}}','{{%request_product}}','request_id','{{%request}}','id');
        $this->addForeignKey('{{%fk_request_product-product_id}}','{{%request_product}}','product_id','{{%product}}','id');
//        $this->addForeignKey('{{%fk_request_product-vproduct_id}}','{{%request_product}}','vproduct_id','v_product','id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk_request_product-request_id}}','{{%request_product}}');
        $this->dropForeignKey('{{%fk_request_product-product_id}}','{{%request_product}}');
//        $this->dropForeignKey('{{%fk_request_product-vproduct_id}}','{{%request_product}}');

        $this->dropIndex('{{%ind_request_product-vproduct_id}}','{{%request_product}}');
        $this->dropIndex('{{%ind_request_product-product_id}}','{{%request_product}}');
        $this->dropIndex('{{%ind_request_product-request_id}}','{{%request_product}}');

        $this->dropTable('{{%request_product}}');
    }
}
