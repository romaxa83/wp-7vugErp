<?php

use yii\db\Migration;
use yii\db\mysql\Schema;

/**
 * Handles the creation of table `access`.
 */
class m180327_082457_create_access extends Migration {

    /**
     * @inheritdoc
     */
    public function up() {
        $this->createTable('access', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING,
            'controller' => Schema::TYPE_STRING,
            'action' => Schema::TYPE_STRING,
            'weight' => $this->smallInteger(4),
            'status' => $this->smallInteger(4)
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down() {
        $this->dropTable('access');
    }

}
