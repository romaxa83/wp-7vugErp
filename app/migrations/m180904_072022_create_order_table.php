<?php

use yii\db\Migration;

class m180904_072022_create_order_table extends Migration {

    public function safeUp() 
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        $this->createTable('order', [
            'id' => $this->primaryKey(),
            'order' => $this->integer(),
            'date' => $this->dateTime(),
            'amount' => $this->decimal(65, 13),
            'status' => $this->smallInteger()
        ],$tableOptions);
        $this->createIndex('idx_order_order', 'order', 'order');
    }

    public function safeDown() {
        $this->dropTable('order');
    }

}
