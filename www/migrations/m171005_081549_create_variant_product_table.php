<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `variant_product`.
 */
class m171005_081549_create_variant_product_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('variant_product', [
            'id' => Schema::TYPE_PK,
            'vendor_code' => Schema::TYPE_TEXT,
            'char_value' => Schema::TYPE_TEXT,
            'prod_id' => Schema::TYPE_INTEGER,//связь
            'amount' => $this->integer()->notNull()->defaultValue(0),
            'start_price' => $this->decimal(11,4)->notNull(),
            'cost_price' => $this->decimal(11,4)->notNull(),
            'trade_price' => $this->decimal(11,4)->notNull(),
            'price1' => $this->decimal(11,2)->notNull(),
            'price2' => $this->decimal(11,2)->notNull(),
            'id_agent' => Schema::TYPE_INTEGER,
            'status' => Schema::TYPE_STRING . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
        ]);

         $this->createIndex('fk_var_prod_prod','variant_product','prod_id');

         $this->addForeignKey('fk_var_prod_prod','variant_product','prod_id','product','id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('variant_product');
    }
}
