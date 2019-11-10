<?php

namespace app\controllers;

use app\models\Settings;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use app\controllers\AccessController;
use yii\web\Response;
use app\models\LoginForm;
use yii\helpers\Json;
use app\service\BazaApi;

class SiteController extends BaseController
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
                        'actions' => ['login'],
                        'roles' => ['?']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['logout','index','error'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['settings','change-usd'],
                        'roles' => ['admin']
                    ],
                ],
            ],
        ];
    }
    /**
     * @inheritdoc
    */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }
    /**
     * Переброс на каталог товаров для авторизованых пользователей , в обратном на страницу авторизаций.
     * @return mixed
    */
    public function actionIndex()
    {
        if(!Yii::$app->user->isGuest){
            $user = User::findOne(Yii::$app->user->id);
            if($user->isRoleManager()){
                return Yii::$app->getResponse()->redirect('/manager/manager/index');
            }
            return Yii::$app->getResponse()->redirect('/product/index');
        }else{
            return Yii::$app->getResponse()->redirect('/site/login');
        }
    }
    /**
     * Логика авторизаций.
     * @return Response|string
    */
    public function actionLogin()
    {
        $this->layout = '/main-login';
        if(!Yii::$app->user->isGuest){
            return $this->goHome();
        }
        $model = new LoginForm();
        if($model->load(Yii::$app->request->post()) && $model->login()){
            return $this->goBack();
        }
        return $this->render('login', ['model' => $model]);
    }
    /**
     * Логика выхода с учетной записи.
     * @return Response
    */
    public function actionLogout()
    {
        Yii::$app->session->destroy();
        Yii::$app->user->logout();
        return $this->goHome();
    }
    /**
     * Вывод страници редактирования настроек
     * @return Response|string
    */
    public function actionSettings()
    {
        $model = Settings::find()->one();
        $post = Yii::$app->request->post();
        if($model->load($post)){
            $model->save();

            $dataApi['requestData']['body'] = $model->usd;
            $dataApi['requestData']['title'] = BazaApi::CHANGE_COURSE;
            $dataApi['data'] = $model->usd;
            (new BazaApi('product','update'))->add($dataApi);

            if($model->hasErrors()){
                ShowMessenge($model->getErrorSummary(true));
            }else{
                ShowMessenge('Настройки успешно сохранены', 'success');
            }
            return $this->redirect(Yii::$app->request->referrer);
        }
        return $this->render('settings',['model' => $model]);
    }
    /**
     * Изменения курса доллара с интерфейса
     * @return Response|string
    */
    public function actionChangeUsd()
    {
        if(Yii::$app->request->isAjax){
            $model = Settings::find()->one();
            $model->usd = abs(Yii::$app->request->post('curr'));
            $model->update();

            $dataApi['requestData']['body'] = $model->usd;
            $dataApi['requestData']['title'] = BazaApi::CHANGE_COURSE;
            $dataApi['data'] = $model->usd;
            (new BazaApi('product','update'))->add($dataApi);

            if($model->hasErrors()){
                ShowMessenge($model->getErrorSummary(true));
            }else{
                ShowMessenge('Курс $ успешно изменнен', 'success');
            }
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionConvertToUa()
    {
        if(Yii::$app->request->isAjax){
            $val = Yii::$app->request->post('val');
            $model = Settings::find()->one();
            return $val * $model->usd;
        }
    }

    public function actionConvertToUsd()
    {
        if(Yii::$app->request->isAjax){
            $val = Yii::$app->request->post('val');
            $model = Settings::find()->one();
            return $val / $model->usd;
        }
    }
}