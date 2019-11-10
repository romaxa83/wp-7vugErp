<?php

$this->title = 'Добавления товара';

echo $this->render('_form-product', [
    'category_form' => $category_form,
    'categories' => $categories,
    'model_agent' => $model_agent,
    'model' => $model,
    'agents' => $agents
]);
