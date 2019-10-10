<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `agent`.
 */
class m170929_064740_create_agent_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('agent', [
            'id' =>  $this->primaryKey(),
            'name' => $this->string(),
            'address' => $this->string(),
            'firm' => $this->string(),
            'telephone' => $this->string(),
            'data' => $this->string(),
            'price_type' => $this->smallInteger(),
            'type' => $this->string(4),
            'status' => $this->smallInteger(),
            'is_main' => $this->smallInteger(),//?
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('agent');
    }
}
