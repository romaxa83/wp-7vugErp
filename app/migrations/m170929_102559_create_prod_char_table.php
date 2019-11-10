<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `cat_char`.
 */
class m170929_102559_create_prod_char_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('prod_char', [
            'id' => Schema::TYPE_PK,
            'id_prod' => Schema::TYPE_INTEGER . ' NOT NULL',
            'id_char' => Schema::TYPE_INTEGER . ' NOT NULL',
        ],$tableOptions);

        $this->addForeignKey('fk_prod_char_id_prod','prod_char','id_prod','product','id');
        $this->addForeignKey('fk_prod_char_id_char','prod_char','id_char','characteristic','id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('prod_char');
        $this->dropForeignKey('fk_prod_char_id_prod', 'prod_char');
        $this->dropForeignKey('fk_prod_char_id_char', 'prod_char');
    }
}
