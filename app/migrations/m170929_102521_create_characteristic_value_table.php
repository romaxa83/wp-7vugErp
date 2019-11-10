<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `characteristic_value`.
 */
class m170929_102521_create_characteristic_value_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        $this->createTable('characteristic_value', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'id_char' => Schema::TYPE_INTEGER . ' NOT NULL',
        ],$tableOptions);
        
        $this->addForeignKey('fk_val_id_char','characteristic_value','id_char','characteristic','id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('characteristic_value');
        $this->dropForeignKey('fk_val_id_char', 'characteristic_value');
    }
}
