<?php

namespace app\controllers;

use Yii;
use app\models\CharacteristicValue;
use app\models\VProduct;
use yii\filters\AccessControl;
use app\controllers\AccessController;
use yii\web\NotFoundHttpException;
use yii\helpers\Json;
/**
 * CharacteristicValueController implements the CRUD actions for CharacteristicValue model.
 */
class CharacteristicValueController extends BaseController
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
                        'actions' =>  ['template'],
                        'roles' => ['characteristic_create','characteristic_update']
                    ],
                    [
                        'allow' => true,
                        'actions' =>  ['create'],
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
                    ]
                ],
            ]
        ];
    }

    /**
     * Отображает форму создание значения характеристик
     * @return mixed
     */
    public function actionTemplate()
    {
        if (Yii::$app->request->isAjax){
            return $this->renderAjax('templateCreate');
        }
    }
    /**
     * Создание значения характеристики по ajax
     * Получает имя значения характеристики и индификатор характеристики
     * @return mixed
     */
    public function actionCreate()
    {   
        if (Yii::$app->request->isAjax){
            $model = new CharacteristicValue();
            $model->name = Yii::$app->request->post('name');
            $model->id_char = Yii::$app->request->post('status');
            $model->save();

            if($model->hasErrors()){
                return JSON::encode(['type'  => 'error', 'msg' =>FormattedMessenge($model->getErrorSummary(true))]);
            } else {
                return JSON::encode([
                    'type'  => 'success',
                    'msg' => FormattedMessenge('Значение характеристики успешно добавлено', 'success'),
                    'html' => $this->renderAjax('templateReady', [
                        'id' => $model->id
                    ])
                ]);
            }
        }
    }

    /**
     * Изменение значения характеристики по ajax
     * Получает имя и индификатор значения характеристики
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (Yii::$app->request->isAjax){
            $model = $this->findModel($id);
            $model->name = Yii::$app->request->post('name');
            $model->update();

            if($model->hasErrors()){
                return JSON::encode(['type'  => 'error', 'msg' =>FormattedMessenge($model->getErrorSummary(true))]);
            } else {
                return JSON::encode([
                    'type'  => 'success',
                    'msg' => FormattedMessenge('Значение характеристики успешно обновлено', 'success'),
                    'name' => $model->name
                ]);
            }
        }
    }
    /**
     * Принимает индикатор значения характеристики
     * Если значение характеристики не привязанно к продукту, удаляет значение характеристики
     * Если привязанна возвращает Json массив имен привязанных продуктов=
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $char_id = $model->id_char;

        $products = VProduct::find()->andFilterWhere(['like', 'char_value', 'i:'.$char_id.';s:'.strlen($id).':"'.$id.'";'])->all();
        foreach ($products as $product){
            $products_name[] = $product->getProduct()->one()->name . '' . VProduct::getCharValueFromId($product->char_value);
        }

        if(isset($products_name)) {
            return JSON::encode(['type'  => 'error', 'products' => $products_name]);
        } else {
            $model->delete();
            return JSON::encode([
                'type'  => 'success',
                'msg' => FormattedMessenge('Значение характеристики успешно удаленно', 'success')
            ]);
        }
    }
    /**
     * Ищет CharacteristicValue model по идентификатору модели
     * @param integer $id
     * @return CharacteristicValue найденная модель
     * @throws NotFoundHttpException Если модель не найденна
     */
    protected function findModel($id)
    {
        if (($model = CharacteristicValue::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
