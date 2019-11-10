<?php

use yii\db\Migration;

/**
 * Class m181030_122427_rename_column_product
 */
class m181030_122427_rename_column_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk_prod_cat_id', 'product');
        $this->dropForeignKey('fk_prod_agent_id', 'product');
        $this->dropIndex('fk_prod_cat_id', 'product');
        $this->dropIndex('fk_prod_agent_id', 'product');
        $this->renameColumn('product','id_category','category_id');
        $this->renameColumn('product','id_agent','agent_id');
        $this->createIndex('fk_prod_cat_id','product','category_id');
        $this->createIndex('fk_prod_agent_id','product','agent_id');
        $this->addForeignKey('fk_prod_cat_id','product','category_id','category','id');
        $this->addForeignKey('fk_prod_agent_id','product','agent_id','agent','id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('fk_prod_cat_id', 'product');
        $this->dropIndex('fk_prod_agent_id', 'product');
        $this->dropForeignKey('fk_prod_cat_id', 'product');
        $this->dropForeignKey('fk_prod_agent_id', 'product');
        $this->renameColumn('product','category_id','id_category');
        $this->renameColumn('product','agent_id','id_agent');
        $this->createIndex('fk_prod_cat_id','product','id_category');
        $this->createIndex('fk_prod_agent_id','product','id_category');
        $this->addForeignKey('fk_prod_cat_id','product','id_category','category','id');
        $this->addForeignKey('fk_prod_agent_id','product','id_category','agent','id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181030_122427_rename_column_product cannot be reverted.\n";

        return false;
    }
    */
}
