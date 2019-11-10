<?php

use yii\db\Migration;

/**
 * Handles the creation of table `oper_archive`.
 */
class m180319_072116_create_oper_archive_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('oper_archive', [
            'id' => $this->primaryKey(),
            'id_transaction' => $this->integer(11),
            'transaction' => $this->string(50),
            'value' => $this->text(),
            'status' => $this->integer()->defaultValue(0),
            'type' => $this->integer(1),
            'date' => $this->string(50),
            'date_archive' => $this->string(50),
        ],$tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('oper_archive');
    }
}
