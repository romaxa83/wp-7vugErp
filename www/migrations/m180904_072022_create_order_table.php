<?php

use yii\db\Migration;

class m180904_072022_create_order_table extends Migration {

    public function safeUp() {
        $this->createTable('order', [
            'id' => $this->primaryKey(),
            'order' => $this->integer(),
            'date' => $this->dateTime(),
            'amount' => $this->decimal(65, 13),
            'status' => $this->smallInteger()
        ]);
        $this->createIndex('idx_order_order', 'order', 'order');
    }

    public function safeDown() {
        $this->dropTable('order');
    }

}
