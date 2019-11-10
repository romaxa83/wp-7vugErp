<?php

namespace app\controllers;

use Yii;
use app\models\Agent;
use app\controllers\AccessController;
use app\controllers\BaseController;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class AgentController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    AccessController::getAccessRules(Yii::$app->controller->id),
                    [
                        'allow' => true,
                        'actions' => ['store', 'provider'],
                        'roles' => ['agent_create','agent_update']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['agent_create'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'change-status', 'change-price'],
                        'roles' => ['agent_update'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['get-price-type'],
                        'roles' => ['operation_update']
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
            ]
        ];
    }
    /** 
     * Метод отображения всех моделей Agent с типо 2 (магазин)
     * @return mixed
    */
    public function actionStore()
    {
        $model = new Agent();
        $stores = Agent::find()->where(['type' => 2])->asArray()->all();
        return $this->render('store',[
            'model' => $model,
            'stores' => $stores
        ]);
    }
    /** 
     * Метод отображения всех моделей Agent с типо 1 (поставщик)
     * @return mixed
    */
    public function actionProvider()
    {
        $model = new Agent();
        $provider = Agent::find()->where(['type' => 1])->asArray()->all();
        return $this->render('provider',[
            'model' => $model,
            'providers' => $provider
        ]);
    }
    /** 
     * Метод для создания новой модели Agent 
     * Работает с Ajax также по стандарту принимает данные с формы 
     * @return mixed
     * @throws ForbiddenHttpException если у юзера нету доступа.
    */
    public function actionCreate()
    {
        $model = new Agent();
        $post = Yii::$app->request->post();
        if(Yii::$app->request->isAjax){
            return $this->actionCreateFromAjax($model,$post['Agent']);
        }
        if ($model->load($post)) {
            $model = $model->saveAgent($post['Agent'],false,$model);
            if($model->hasErrors()){
                ShowMessenge($model->getErrorSummary(true));
            }else{
                ShowMessenge('Агент удачно создан','success');
            }
            if($model->type == 2) {
                return $this->redirect('/agent/store');
            }else{
                return $this->redirect('/agent/provider');
            }
        }else{
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    /** 
     * Всопогательный метод для создания модели Agent так же генерирует список агентов в ответе либо ошибку 
     * @param object $model модель Agent
     * @param array $data данные присланые ajax (firm,adress,telephone,data)
     * @return json
    */
    private function actionCreateFromAjax($model,$data)
    {
        $model->saveAgent($data,true,$model);
        if($model->hasErrors()){
            $error = FormattedMessenge($model->getErrorSummary(true),'danger',true);
            return JSON::encode(['type'  => 'error','msg' => $error]);
        }
        return JSON::encode(['type'  => 'success','data' => $model]);  
    }
    /**
     * Метод для обновления модели Agent 
     * @param integer $id индефикатор модели
     * @return mixed
     * @throws ForbiddenHttpException если у юзера нету доступа.
    */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            $model = $model->saveAgent($post['Agent'],false,$model);
            if($model->hasErrors()){
                ShowMessenge($model->getErrorSummary(true));
            }else{
                ShowMessenge('Агент удачно обновлен','success');
            }
            if($model->type == 2) {
                return $this->redirect('/agent/store');
            }else{
                return $this->redirect('/agent/provider');
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }
    /**
     * Изменения статуса модели Agent работает по ajax 
     * принимает 
     * $id - индефикатор модели Agent
     * $status - 1/0 (включен/выключен)
     * @return mixed
    */
    public function actionChangeStatus()
    {
        if(Yii::$app->request->isAjax){
            $id = Yii::$app->request->post('id');
            $status = Yii::$app->request->post('status');
            $model = $this->findModel($id);
            $model->status = $status;
            return $model->update();
        }
    }
    /**
     * Изменения цены для модели Agent с типом 1(магазин) работает по ajax 
     * принимает 
     * $post['id'] - индефикатор модели Agent
     * $post['price'] - 1/2 тип цены
     * @return boolean
    */
    public function actionChangePrice()
    {
        if (Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $store = Agent::find()->where(['id' => $post['id']])->one();
            if ($store->price_type != $post['price']){
                $store->price_type = $post['price'];
                $store->save();
                return true;
            }
            return false;
        }
    }
    /**
     * 
    */
    public function actionGetPriceType()
    {
        if(Yii::$app->request->isAjax){
            $agent = Agent::find()->asArray()->select('price_type')->where(['id'=>Yii::$app->request->post('id')])->one();
            return $agent['price_type'];
        }
    }
    /**
     * Поиск модели Agent по индефикатору
     * @param integer $id
     * @return Agent model
     * @throws NotFoundHttpException Если не найдена модель
    */
    protected function findModel($id)
    {
        if (($model = Agent::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
