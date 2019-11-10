<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use app\models\Characteristic;
use app\models\CharacteristicValue;
use app\models\VProduct;
use app\models\CatChar;
use app\controllers\AccessController;
/**
 * CharacteristicController implements the CRUD actions for Characteristic model.
 */
class CharacteristicController extends BaseController
{   
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    AccessController::getAccessRules(Yii::$app->controller->id),
                    [
                        'allow' => true,
                        'actions' =>  ['index', 'create', 'create-form'],
                        'roles' => ['characteristic_create']
                    ],
                    [
                        'allow' => true,
                        'actions' =>  ['update'],
                        'roles' => ['characteristic_update']
                    ],
                    [
                        'allow' => true,
                        'actions' =>  ['delete'],
                        'roles' => ['characteristic_delete']
                    ],
                ],

            ]
        ];
    }
    /**
     * Отображает список характеристик
     * @return mixed
     */
    public function actionIndex()
    {
        $values = Characteristic::find()->orderBy('name')->asArray()->all();
        $model = new Characteristic();
        return $this->render('index', [
            'values' => $values,
            'model' => $model,
        ]);
    }
    /**
     * Отображает форму создание характеристик
     * @return mixed
     */
    public function actionCreateForm()
    {
        $model = new Characteristic();

        return $this->renderAjax('_form-characteristic' , [
            'model' => $model,
        ]);
    }

    /**
     * Создает новую характеристику
     * @return mixed
     */
    public function actionCreate()
    {
        $post = Yii::$app->request->post();
        $isAjax = Yii::$app->request->isAjax;
        $model = new Characteristic();
        if($model->load($post)) {
            $model->save();
            if($isAjax){
                if($model->hasErrors()){
                    return JSON::encode(['type'  => 'error','msg' => FormattedMessenge($model->getErrorSummary(true))]);
                } else {
                    return JSON::encode([
                        'type'  => 'success',
                        'msg' => FormattedMessenge('Характеристика успешно созданна', 'success'),
                        'name' => $model->name,
                        'id' => $model->id
                    ]);
                }
            } else {
                if($model->hasErrors()){
                    ShowMessenge($model->getErrorSummary(true));
                } else {
                    ShowMessenge('Характеристика успешно созданна', 'success');
                }
            }
        }
        return $this->redirect(Yii::$app->request->referrer);
    }
    /**
     * Обновляет характеристику. Принимает индикатор(get) и имя(ajax) характеристики
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (Yii::$app->request->isAjax){
            $model = Characteristic::findOne($id);
            $model->name = Yii::$app->request->post('name');
            $model->update();

            if($model->hasErrors()){
                return JSON::encode(['type'  => 'error','msg' => FormattedMessenge($model->getErrorSummary(true))]);
            } else {
                return JSON::encode([
                    'type'  => 'success',
                    'msg' => FormattedMessenge('Характеристика успешно обновленна', 'success'),
                    'name' => $model->name
                ]);
            }
        }
    }
    /**
     * Принимает индикатор характеристики
     * Если характеристика не привязанна к продукту, удаляет характеристику, все значения и привязку к категории
     * Если привязанна возвращает Json массив имен привязанных продуктов
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $values = CharacteristicValue::find()->select('id')->where(['id_char' => $id])->asArray()->all();

        foreach ($values as $value){
            $products = VProduct::find()->andFilterWhere(['like', 'char_value', 'i:'.$id.';s:'.strlen($value['id']).':"'.$value['id'].'";'])->all();
            foreach ($products as $product){
                $products_name[] = $product->getProduct()->one()->name . '' . VProduct::getCharValueFromId($product->char_value);
            }
        }

        if(isset($products_name)) {
            return JSON::encode(['type'  => 'error', 'products' => $products_name]);
        } else {
            Characteristic::findOne($id)->delete();
            CatChar::deleteAll(['char_id' => $id]);
            CharacteristicValue::deleteAll(['id_char' => $id]);
            return JSON::encode(['type'  => 'success', 'msg' => FormattedMessenge('Характеристика успешно удаленна', 'success')]);
        }
    }


}
