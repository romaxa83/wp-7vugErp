<?php

use yii\db\Migration;

/**
 * Class m180518_080108_create_v_product
 */
class m180518_080108_create_v_product extends Migration
{
    private $tableOptions;

    public function init()
    {
        parent::init();
        if ($this->db->driverName === 'mysql') {
            /** @see https://stackoverflow.com/questions/766809 */
            $this->tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
    }
    
    public function safeUp()
    {
        $this->createTable('v_product', [
            'id' => $this->bigPrimaryKey(),
            'product_id' => $this->integer(11),
            'amount' => $this->integer(50),
            'price1' => $this->double(),
            'price2' => $this->double(),
            'char_value' => $this->text(),
            'date_create' => $this->date(),
            'date_update' => $this->date(),
        ], $this->tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('v_product');
    }
}
