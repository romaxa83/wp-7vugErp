<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $cats */
/* @var $parent_cats */
/* @var $chars */

$this->title = 'Категории';
$this->params['breadcrumbs'][] = $this->title;
?>

<section class="content">
    <div class="news-index">
        <div class="row">
            <div class="col-xs-12 col-sm-12 text-right control-btn">
                <?php if (Yii::$app->user->can('category_create')):?>
                    <?= Html::button('Добавить категорию',['class' => 'show-form snap','data-form' => 'category','title' => 'Появится форма добавления родительской категории'])?>
                    <?= Html::button('Отменить',['class' => 'hide-form snap _gray','data-form' => 'category','title' => 'Убрать форму'])?>
                <?php endif;?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div id="category-form" class="hidden">
                    <?php echo $this->render('_form-category',[
                        'model_cat' => $model,
                        'parent_cats' => $parent_cats,
                        'chars_cat' => $chars
                    ]); ?>
                </div>
            </div>
        </div>
        <div class="row" id="box-category">
            <div class="col-xs-12">
                <?php if(!empty($cats)):?>
                    <?php foreach ($cats as $id => $parent): ?>
                        <div class="form-group form-check">
                            <div class="input-group">
                                <a class="expand-plus form-control collapsed" data-toggle="collapse" href="#custom<?= $parent['parent']['id'] ?>" aria-expanded="false">
                                    <?= $parent['parent']['name'] ?>
                                </a>
                                <div class="input-group-addon addon-transparent size2">
                                    <?php if (Yii::$app->user->can('category_update')):?>
                                        <a href="/category/update?id=<?= $parent['parent']['id'] ?>" type="button" class="btn no-btn" ><i class="fa fa-pencil"></i></a>
                                    <?php endif;?>
                                    <?php if (Yii::$app->user->can('admin')): ?>
                                        <input id="category-status_<?= $parent['parent']['id'] ?>" class="change-status-category" type="checkbox" <?= $parent['parent']['status'] == 1 ? 'checked' : null; ?>  data-id="<?= $parent['parent']['id'] ?>">
                                        <label for="category-status_<?= $parent['parent']['id'] ?>" data-text-true="Вкл" data-text-false="Выкл" title="Статус для базы"><i></i></label>

                                        <input id="publish-status_<?= $parent['parent']['id'] ?>" class="publish_status publish_category" type="checkbox" <?= $parent['parent']['publish_status'] == 1 ? 'checked' : null; ?>  data-id="<?= $parent['parent']['id'] ?>" data-type="category">
                                        <label for="publish-status_<?= $parent['parent']['id'] ?>" data-text-true="Вкл" data-text-false="Выкл" title="Публиковать ли на магазин"><i></i></label>
                                    <?php endif;?>
                                </div>
                            </div>
                        </div>
                        <div class="collapse" id="custom<?= $parent['parent']['id'] ?>">
                            <div class="form-group level-2">
                                <?php if (Yii::$app->user->can('category_create')):?>
                                    <div class="input-group">
                                        <a class="expand-plus form-control create-sub-cat" href="/category/create?id=<?= $parent['parent']['id'] ?>" aria-expanded="true">Добавить подкатегорию</a>
                                        <div class="input-group-addon addon-transparent size3"></div>
                                    </div>
                                <?php endif;?>
                            </div>
                            <?php $sub_cats = $parent['child'] ?>
                            <?php if (!empty($sub_cats)):?>
                                <?php foreach ($sub_cats as $sub_cat):?>
                                    <div class="form-group form-check level-2">
                                        <div class="input-group">
                                            <a class="expand-plus form-control collapsed" data-toggle="collapse" href="#custom<?= $sub_cat['parent']['id'] ?>" aria-expanded="false"><?= $sub_cat['parent']['name'] ?></a>
                                            <div class="input-group-addon addon-transparent size2">
                                                <?php if (Yii::$app->user->can('category_update')):?>
                                                    <a href="/category/update?id=<?= $sub_cat['parent']['id'] ?>" type="button" class="btn no-btn" ><i class="fa fa-pencil"></i></a>
                                                    <?php if (Yii::$app->user->can('admin')): ?>
                                                        <input id="category-status_<?= $sub_cat['parent']['id'] ?>" class="change-status-category" type="checkbox" <?= $sub_cat['parent']['status'] == 1 ? 'checked' : null; ?>  data-id="<?= $sub_cat['parent']['id'] ?>">
                                                        <label for="category-status_<?= $sub_cat['parent']['id'] ?>" data-text-true="Вкл" data-text-false="Выкл" title="Статус для базы"><i></i></label>

                                                        <input id="publish-status_<?= $sub_cat['parent']['id'] ?>" class="publish_status publish_category" type="checkbox" <?= $sub_cat['parent']['publish_status'] == 1 ? 'checked' : null; ?>  data-id="<?= $sub_cat['parent']['id'] ?>" data-type="category">
                                                        <label for="publish-status_<?= $sub_cat['parent']['id'] ?>" data-text-true="Вкл" data-text-false="Выкл" title="Публиковать ли на магазин"><i></i></label>
                                                    <?php endif;?>
                                                <?php endif;?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="collapse" id="custom<?=  $sub_cat['parent']['id'] ?>">
                                        <div class="form-group level-3">
                                            <?php if (Yii::$app->user->can('category_create')):?>
                                                <div class="input-group">
                                                    <a class="expand-plus form-control create-sub-cat" href="/category/create?id=<?= $sub_cat['parent']['id'] ?>" aria-expanded="true">Добавить подкатегорию</a>
                                                    <div class="input-group-addon addon-transparent size2"></div>
                                                </div>
                                            <?php endif;?>
                                        </div>
                                        <?php $sub_sub_cats = $sub_cat['child'] ?>
                                        <?php if (!empty($sub_sub_cats)):?>
                                            <?php foreach ($sub_sub_cats as $sub_sub_cat):?>
                                                <div class="form-group form-check level-3">
                                                    <div class="input-group">
                                                        <a class="expand-plus form-control collapsed" data-toggle="collapse" href="#custom<?=  $sub_sub_cat['parent']['id'] ?>" aria-expanded="false"><?= $sub_sub_cat['parent']['name'] ?></a>
                                                        <div class="input-group-addon addon-transparent size2">
                                                            <?php if (Yii::$app->user->can('category_update')):?>
                                                                <a href="/category/update?id=<?= $sub_sub_cat['parent']['id'] ?>" type="button" class="btn no-btn" ><i class="fa fa-pencil"></i></a>
                                                                <?php if (Yii::$app->user->can('admin')): ?>
                                                                    <input id="category-status_<?= $sub_sub_cat['parent']['id'] ?>" class="change-status-category" type="checkbox" <?= $sub_sub_cat['parent']['status'] == 1 ? 'checked' : null; ?>  data-id="<?= $sub_sub_cat['parent']['id'] ?>">
                                                                    <label for="category-status_<?= $sub_sub_cat['parent']['id'] ?>" data-text-true="Вкл" data-text-false="Выкл" title="Статус для базы"><i></i></label>

                                                                    <input id="publish-status_<?= $sub_sub_cat['parent']['id'] ?>" class="publish_status publish_category" type="checkbox" <?= $sub_sub_cat['parent']['publish_status'] == 1 ? 'checked' : null; ?>  data-id="<?= $sub_sub_cat['parent']['id'] ?>" data-type="category">
                                                                    <label for="publish-status_<?= $sub_sub_cat['parent']['id'] ?>" data-text-true="Вкл" data-text-false="Выкл" title="Публиковать ли на магазин"><i></i></label>
                                                                <?php endif;?>
                                                            <?php endif;?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="collapse" id="custom<?=  $sub_sub_cat['parent']['id'] ?>">
                                                    <div class="form-group level-4">
                                                        <?php if (Yii::$app->user->can('category_create')):?>
                                                            <div class="input-group">
                                                                <a class="expand-plus form-control create-sub-cat" href="/category/create?id=<?= $sub_sub_cat['parent']['id'] ?>" aria-expanded="true">Добавить подкатегорию</a>
                                                                <div class="input-group-addon addon-transparent size2"></div>
                                                            </div>
                                                        <?php endif;?>
                                                    </div>
                                                    <?php $sub_sub_sub_cats = $sub_sub_cat['child'] ?>
                                                    <?php if (!empty($sub_sub_sub_cats)):?>
                                                        <?php foreach ($sub_sub_sub_cats as $sub_sub_sub_cat):?>
                                                            <div class="form-group form-check level-4">
                                                                <div class="input-group">
                                                                    <a class="expand-plus form-control collapsed" data-toggle="collapse" href="#custom<?= $sub_sub_sub_cat['parent']['id'] ?>" aria-expanded="false"><?= $sub_sub_sub_cat['parent']['name'] ?></a>
                                                                    <div class="input-group-addon addon-transparent size2">
                                                                        <?php if (Yii::$app->user->can('category_update')):?>
                                                                            <a href="/category/update?id=<?= $sub_sub_sub_cat['parent']['id'] ?>" type="button" class="btn no-btn" ><i class="fa fa-pencil"></i></a>
                                                                            <?php if (Yii::$app->user->can('admin')): ?>
                                                                                <input id="category-status_<?= $sub_sub_sub_cat['parent']['id'] ?>" class="change-status-category" type="checkbox" <?= $sub_sub_sub_cat['parent']['status'] == 1 ? 'checked' : null; ?>  data-id="<?= $sub_sub_sub_cat['parent']['id'] ?>">
                                                                                <label for="category-status_<?= $sub_sub_sub_cat['parent']['id'] ?>" data-text-true="Вкл" data-text-false="Выкл" title="Статус для базы"><i></i></label>

                                                                                <input id="publish-status_<?= $sub_sub_sub_cat['parent']['id'] ?>" class="publish_status publish_category" type="checkbox" <?= $sub_sub_sub_cat['parent']['publish_status'] == 1 ? 'checked' : null; ?>  data-id="<?= $sub_sub_sub_cat['parent']['id'] ?>" data-type="category">
                                                                                <label for="publish-status_<?= $sub_sub_sub_cat['parent']['id'] ?>" data-text-true="Вкл" data-text-false="Выкл" title="Публиковать ли на магазин"><i></i></label>
                                                                            <?php endif;?>
                                                                        <?php endif;?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="collapse" id="custom<?=  $sub_sub_sub_cat['parent']['id'] ?>">
                                                                <div class="form-group level-5">
                                                                    <?php if (Yii::$app->user->can('category_create')):?>
                                                                        <div class="input-group">
                                                                            <a class="expand-plus form-control create-sub-cat" href="/category/create?id=<?= $sub_sub_sub_cat['parent']['id'] ?>" aria-expanded="true">Добавить подкатегорию</a>
                                                                            <div class="input-group-addon addon-transparent size2"></div>
                                                                        </div>
                                                                    <?php endif;?>
                                                                </div>
                                                                <?php $sub_sub_sub_sub_cats = $sub_sub_sub_cat['child'] ?>
                                                                <?php if (!empty($sub_sub_sub_sub_cats)):?>
                                                                    <?php foreach ($sub_sub_sub_sub_cats as $sub_sub_sub_sub_cats):?>
                                                                        <div class="form-group form-check level-5">
                                                                            <div class="input-group">
                                                                                <a class="expand-plus form-control collapsed" data-toggle="collapse" href="#custom<?= $sub_sub_sub_sub_cats['parent']['id'] ?>" aria-expanded="false"><?= $sub_sub_sub_sub_cats['parent']['name'] ?></a>
                                                                                <div class="input-group-addon addon-transparent size2">
                                                                                    <?php if (Yii::$app->user->can('category_update')):?>
                                                                                        <a href="/category/update?id=<?= $sub_sub_sub_sub_cats['parent']['id'] ?>" type="button" class="btn no-btn" ><i class="fa fa-pencil"></i></a>
                                                                                        <?php if (Yii::$app->user->can('admin')): ?>
                                                                                            <input id="category-status_<?= $sub_sub_sub_sub_cats['parent']['id'] ?>" class="change-status-category" type="checkbox" <?= $sub_sub_sub_sub_cats['parent']['status'] == 1 ? 'checked' : null; ?>  data-id="<?= $sub_sub_sub_sub_cats['parent']['id'] ?>">
                                                                                            <label for="category-status_<?= $sub_sub_sub_sub_cats['parent']['id'] ?>" data-text-true="Вкл" data-text-false="Выкл" title="Статус для базы"><i></i></label>

                                                                                            <input id="publish-status_<?= $sub_sub_sub_sub_cats['parent']['id'] ?>" class="publish_status publish_category" type="checkbox" <?= $sub_sub_sub_sub_cats['parent']['publish_status'] == 1 ? 'checked' : null; ?>  data-id="<?= $sub_sub_sub_sub_cats['parent']['id'] ?>" data-type="category">
                                                                                            <label for="publish-status_<?= $sub_sub_sub_sub_cats['parent']['id'] ?>" data-text-true="Вкл" data-text-false="Выкл" title="Публиковать ли на магазин"><i></i></label>
                                                                                        <?php endif;?>
                                                                                    <?php endif;?>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach;?>
                                                                <?php endif;?>
                                                            </div>
                                                        <?php endforeach;?>
                                                    <?php endif;?>
                                                </div>
                                            <?php endforeach;?>
                                        <?php endif;?>
                                    </div>
                                <?php endforeach;?>
                            <?php endif;?>
                        </div>
                    <?php endforeach;?>
                <?php endif;?>
            </div>
        </div>
    </div>
</section>
