<?php

namespace app\repositories;

use app\models\Product;

class ProductRepository
{
    /**
     * возвращает товары по 100 штук
     * @return iterable|Product[]
     */
    public function getAllIterator()
    {
        return Product::find()->where(['publish_status' => Product::STATUS_SHOP_ACTIVE])->all();
    }

    public function getAllIteratorUpdate($period)
    {
        $time = time() - $period;

        return Product::find()
            ->where(['publish_status' => Product::STATUS_SHOP_ACTIVE])
            ->andWhere(['>','updated_at',$time])
            ->all();
    }

    public function getAllIteratorDelete($period)
    {
        $time = time() - $period;

        return Product::find()
            ->where(['publish_status' => Product::STATUS_SHOP_DRAFT])
            ->andWhere(['>','updated_at',$time])
            ->all();
    }
}