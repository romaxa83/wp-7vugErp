<?php

use yii\db\Migration;
use yii\db\mysql\Schema;

/**
 * Handles the creation of table `page_size`.
 */
class m171108_151839_create_settings_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        $this->createTable('settings', [
            'id' => Schema::TYPE_PK,
            'usd' => $this->decimal(11,2)->notNull(),
            'per_trade_price' => $this->decimal(11,2)->notNull(),
            'cat' => Schema::TYPE_INTEGER,
            'prod' => Schema::TYPE_INTEGER,
            'operation' => Schema::TYPE_INTEGER,
            'store' => Schema::TYPE_INTEGER,
            'user' => Schema::TYPE_INTEGER,
            'price_list' => Schema::TYPE_INTEGER,
            'boss' => Schema::TYPE_STRING,
            'name_firm' => Schema::TYPE_STRING,
            'address' => Schema::TYPE_TEXT,
            'property' => Schema::TYPE_TEXT,
            'float_ua' => Schema::TYPE_INTEGER,
            'float_usd' => Schema::TYPE_INTEGER,
        ],$tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('settings');
    }
}
