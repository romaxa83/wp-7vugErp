<?php

use yii\db\Migration;

/**
 * Class m191003_123324_added_to_user_field_old_auth_key
 */
class m191003_123324_added_to_user_field_old_auth_key extends Migration
{
    /**
    * {@inheritdoc}
    */
    public function safeUp()
    {
        $this->addColumn('user', 'old_auth_key',$this->string(32));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'old_auth_key');
    }
}
