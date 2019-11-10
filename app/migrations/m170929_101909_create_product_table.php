<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `product`.
 */
class m170929_101909_create_product_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        $this->createTable('product', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'vendor_code' => Schema::TYPE_INTEGER . ' NOT NULL',
            'id_category' => Schema::TYPE_INTEGER . ' NOT NULL',
            'id_char' => Schema::TYPE_INTEGER,//?
            'id_agent' => Schema::TYPE_INTEGER . ' NOT NULL',
            'amount' => $this->integer()->notNull()->defaultValue(0),
            'unit' => Schema::TYPE_STRING ,
            'start_price' => $this->decimal(11,4)->notNull(),
            'cost_price' => $this->decimal(11,4)->notNull(),
            'trade_price' => $this->decimal(11,4)->notNull(),
            'price1' => $this->decimal(11,2)->notNull(),
            'price2' => $this->decimal(11,2)->notNull(),
            'is_variant' => Schema::TYPE_SMALLINT . ' NOT NULL',
            'status' => Schema::TYPE_STRING . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ],$tableOptions);

        $this->createIndex('fk_prod_cat_id','product','id_category');
        $this->createIndex('fk_prod_agent_id','product','id_agent');

        $this->addForeignKey('fk_prod_cat_id','product','id_category','category','id');
        $this->addForeignKey('fk_prod_agent_id','product','id_agent','agent','id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_prod_cat_id', 'product');
        $this->dropForeignKey('fk_prod_agent_id', 'product');
        $this->dropTable('product');

    }
}
