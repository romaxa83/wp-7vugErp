<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `category`.
 */
class m170929_101813_create_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('category', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'parent_id' => $this->integer()->notNull()->defaultValue(0),
            'position' => $this->integer()->notNull()->defaultValue(0),

//            'status' => $this->string(4)->notNull()->defaultValue('on'),


            'status' => Schema::TYPE_STRING . ' NOT NULL',

        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('category');
    }
}
