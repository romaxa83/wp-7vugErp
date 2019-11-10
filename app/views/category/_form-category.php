<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/* @var $model_cat app\models\Category*/
/* @var $parent_cats */
/* @var $chars_cat */
$form = ActiveForm::begin(['action' => (Yii::$app->controller->action->id == 'update') ? '/category/update?id=' . Yii::$app->request->get('id') : '/category/create?id=' . Yii::$app->request->get('id')]);
?>
<div class="row">
    <div class="col-md-6 form-group">
        <?php 
            if(empty($model_cat->parent_id) || Yii::$app->controller->action->id == 'update'){
                echo $form->field($model_cat, 'parent_id')->textInput()->dropDownList($parent_cats,['prompt' => 'Выберите родительскую категорию','class' => 'form-control'])->label('');
            }else{
        ?>
        <label class="control-label">Родительская категория</label>
        <p>
        <?php 
                echo $parent_cats[$model_cat->parent_id];
            }
        ?>
        </p>
        <?php 
            echo $form->field($model_cat, 'name')->textInput(['maxlength' => true,'placeholder' => 'Категория'])->label('Имя категории *');
 
            if(Yii::$app->controller->id == 'category'){
                echo $form->field($model_cat, 'position')->input('number',['class' => 'form-control']);
            } 
        ?>
        <div class="form-group">
            <?php
                echo Html::submitButton('Сохранить', ['class' => (Yii::$app->controller->id == 'category') ? 'snap' : 'snap send-category']);
                
                if(Yii::$app->controller->id === 'category' && Yii::$app->controller->action->id !== 'index'){
                    echo Html::a('Отмена',['/category/index'],['class' => 'snap _gray']);
                }else{
                    echo Html::button('Отмена',['class' => 'hide-form snap _gray','data-form' => 'category']);
                }
            ?>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row chars-category">
            <div class="col-md-6">
                <?php 
                    if(Yii::$app->controller->id == 'category'){
                        Pjax::begin([
                            'enablePushState' => false ,
                            'timeout' => 5000,
                            'id' => 'add-char',
                        ]);
                            echo Html::a('добавить характеристику',['/characteristic/create-form'],
                                [
                                    'style' => 'cursor:pointer',
                                    'class' => 'btn btn-success'
                                ]
                            );
                        Pjax::end();
                    }
                ?>
            </div>
            <div class="text-right col-md-6">
                <input id="category-status" name="Category[status]" type="checkbox" <?= ($model_cat->status == 1) ? 'checked' : '' ?>>
                <label for="category-status" data-text-true="Вкл" data-text-false="Выкл" title="Статус для базы"><i></i></label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= 
                    $form->field($model_cat,'charsName')->checkboxList($chars_cat)->label('Хар-тики')  
                ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>