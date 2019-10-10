<?php

use yii\db\Migration;

/**
 * Class m180124_111930_default_settings
 */
class m180124_111930_default_settings extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $model = new \app\models\Settings();

        $model->usd = 26;
        $model->per_trade_price = 10;
        $model->cat = 10;
        $model->prod = 10;
        $model->operation = 10;
        $model->store = 10;
        $model->user = 10;
        $model->price_list = 10;
        $model->mes_change_price = 3;
        $model->float_ua = 3;
        $model->float_usd = 3;
        $model->save();

        echo 'Base value added to settings';
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180124_111930_default_settings cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180124_111930_default_settings cannot be reverted.\n";

        return false;
    }
    */
}
