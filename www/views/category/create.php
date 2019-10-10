<?php 

$this->title = 'Создание категорий';
$this->params['breadcrumbs'][] = ['label' => 'Категории', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Cоздание ';
?>
<div class="content">
    <div id="category-form">
        <?php echo $this->render('_form-category',[
            'model_cat' => $model_cat,
            'parent_cats' => $parent_cats,
            'chars_cat' => $chars_cat
        ]);
        ?>
    </div>
</div>
