<?php

use backend\modules\menu\MenuAsset;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

MenuAsset::register($this);
if (isset($page)) {
    $model->name = $page['name'];
    $model->alias = $page['alias'];
    $model->name_en = $page['name_en'];
    $model->alias_en = $page['alias_en'];
    $model->name_ua = $page['name_ua'];
    $model->alias_ua = $page['alias_ua'];
    $model->type = $page['type'];
    $model->status = $page['status'];
    $action = 'edit-menu?id=' . $page['id'];
    $submit = 'Редактировать';
} else {
    $model->type = Yii::$app->request->get('type');
    $model->status = 0;
    $action = 'add-menu';
    $submit = 'Сохранить';
}
?>

<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Форма для заполнения</h3>
            </div>
            <div class="box-body">
                <?php
                $form = ActiveForm::begin([
                            'id' => 'form-menu',
                            'method' => 'POST',
                            'action' => Url::to('/admin/menu/menu/' . $action)
                ]);
                ?>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#ru" data-toggle="tab" aria-expanded="true" class="ru" style="color: #949ba2;">Русский</a>
                    </li>
                    <li class="">
                        <a href="#en" data-toggle="tab" aria-expanded="false" class="en" style="color: #949ba2;">Английский</a>
                    </li>
                    <li class="">
                        <a href="#ua" data-toggle="tab" aria-expanded="false" class="ua" style="color: #949ba2;">Украинский</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade active in" id="ru">
                        <?php echo $form->field($model, 'name'); ?>
                        <?php echo $form->field($model, 'alias'); ?>
                    </div>
                    <div class="tab-pane fade" id="en">
                        <?php echo $form->field($model, 'name_en'); ?>
                        <?php echo $form->field($model, 'alias_en'); ?>
                    </div>
                    <div class="tab-pane fade" id="ua">
                        <?php echo $form->field($model, 'name_ua'); ?>
                        <?php echo $form->field($model, 'alias_ua'); ?>
                    </div>
                </div>
                <?php echo $form->field($model, 'parent')->dropDownList([]); ?>
                <?php echo $form->field($model, 'type')->dropDownList($type_option); ?>
                <?php
                echo $form->field($model, 'status')->inline()->radioList([1 => 'Да', 0 => 'Нет'], [
                    'item' => function($index, $label, $name, $checked, $value) {
                        $check = $checked ? ' checked="checked"' : '';
                           $return = '<label class="mr-15">';
                           $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" '.$check.' class="custom-radio">';
                           $return .= '<span>' . ucwords($label) . '</span>';
                           $return .= '</label>';
                        return $return;
                    }
                ]);
                ?>
                <div class="form-group">
                    <?php echo Html::submitButton($submit, ['class' => 'btn btn-primary mr-15', 'name' => 'save', 'value' => '/menu/menu']) ?>
                    <?php echo (!isset($page)) ? Html::submitButton('Сохранить и создать новый', ['class' => 'btn btn-primary mr-15', 'name' => 'save', 'value' => '/menu/menu/add-menu?type=' . $model->type]): FALSE; ?>
                    <a href="<?php echo Url::to(['/menu/menu']) ?>" class="btn btn-danger mr-15">Отмена</a>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>