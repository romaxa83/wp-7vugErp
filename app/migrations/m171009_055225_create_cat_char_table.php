<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `cat_char`.
 */
class m171009_055225_create_cat_char_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        $this->createTable('cat_char', [
            'id' => Schema::TYPE_PK,
            'cat_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'char_id' => Schema::TYPE_INTEGER . ' NOT NULL',
        ],$tableOptions);

        $this->addForeignKey('fk_cat_char_cat_id','cat_char','cat_id','category','id','CASCADE','CASCADE');
        $this->addForeignKey('fk_cat_char_char_id','cat_char','char_id','characteristic','id','CASCADE','CASCADE');

    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('cat_char');
    }
}
