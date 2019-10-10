<?php

use yii\db\Migration;

class m180508_123506_add_agents_field_in_product_table extends Migration {

    public function safeUp() {
        $this->addColumn('product', 'agents', $this->text()->null());
    }

    public function safeDown() {
        $this->dropColumn('product', 'agents');
    }

}
