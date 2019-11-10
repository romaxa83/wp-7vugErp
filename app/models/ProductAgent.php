<?php

namespace app\models;

/**
 * This is the model class for table "product_agent".
 *
 * @property int $id
 * @property int $product_id
 * @property int $agent_id
 */
class ProductAgent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_agent';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'agent_id'], 'required'],
            [['product_id', 'agent_id'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'agent_id' => 'Agent ID',
        ];
    }
}
