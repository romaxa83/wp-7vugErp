<?php

use yii\db\Migration;
use yii\db\mysql\Schema;

/**
 * Handles the creation of table `operations`.
 */
class m171108_153011_create_operations_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('operations', [
            'id' => Schema::TYPE_PK,
            'transaction' => Schema::TYPE_INTEGER,
            'operation_id' => Schema::TYPE_INTEGER,
            'old_value' => Schema::TYPE_TEXT,
            'whence' => Schema::TYPE_INTEGER,
            'where' => Schema::TYPE_INTEGER,
            'prod_value' => Schema::TYPE_TEXT,
            'status' => $this->integer()->defaultValue(0),
            'type' => Schema::TYPE_INTEGER,
            'date' => Schema::TYPE_TEXT,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('operations');
    }
}
