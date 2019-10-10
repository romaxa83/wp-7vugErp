<?php

use yii\db\Migration;

/**
 * Handles the creation of table `product_request`.
 */
class m180604_061745_create_product_request_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('product_request', [
            'id' => $this->primaryKey(),
            'prod_value' => $this->text(),
            'comment' => $this->text(),
            'store_id' => $this->integer()->notNull(),
            'status' => $this->integer(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('fk_prod_request_store_id','product_request','store_id');

        $this->addForeignKey('fk_prod_request_store_id','product_request','store_id','agent','id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_prod_request_store_id', 'product_request');

        $this->dropTable('product_request');
    }
}
