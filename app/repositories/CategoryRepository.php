<?php

namespace app\repositories;

use app\models\Category;

class CategoryRepository
{
    /**
     * возвращает категории
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAll()
    {
        return Category::find()->all();
    }
}