<?php

use yii\db\Migration;

/**
 * Handles the creation of table `temp`.
 */
class m180504_092746_create_temp_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        $this->createTable('temp', [
            'id' => $this->primaryKey(),
            'transaction_id' => $this->integer(),
            'product_id' => $this->integer(),
            'old_cost_price' => $this->decimal(11,4),
            'type' => $this->string(1),
        ],$tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('temp');
    }
}
