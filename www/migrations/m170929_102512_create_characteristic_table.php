<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `characteristic`.
 */
class m170929_102512_create_characteristic_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('characteristic', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'status' => Schema::TYPE_SMALLINT . ' NOT NULL',

        ]);

        // $this->createIndex('fk_char_id_product','characteristic','id_product');

        // $this->addForeignKey('fk_char_id_product','characteristic','id_product','product','id');

    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('characteristic');
    }
}
