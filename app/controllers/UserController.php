<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Agent;
use app\models\AuthItem;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use app\modules\manager\models\Request;
use app\controllers\AccessController;

class UserController extends BaseController
{
    /**
     * Доступы к событиям только пользователям с разрешением user_add_view
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
                        'actions' =>  ['index', 'create', 'update', 'delete'],
                        'roles' => ['user_add_view']
                    ]
                ]
            ]
        ];
    }
    /**
     * Рендеринг таблицы пользователей
     * @return mixed
    */
    public function actionIndex()
    {
        $model = new User();
        $users = User::find()->select(['id','username','role','email','password'])->asArray()->all();
        $stores = Agent::getStoresWithManager();
        $roles = ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'description');
        $permissions = ArrayHelper::map(Yii::$app->authManager->getPermissions(), 'name', 'description');
        $role_model = new AuthItem();
        return $this->render('index', [
            'model' => $model,
            'users' => $users,
            'stores' => $stores,
            'roles' => $roles,
            'permissions' => $permissions,
            'role_model' => $role_model
        ]);
    }
    /**
     * Добавление нового пользователя
     * @return mixed
    */
    public function actionCreate()
    {
        $post = Yii::$app->request->post();
        $model = new User();
        if ($model->load($post)){
            $model->save();
            if($model->hasErrors()){
                ShowMessenge($model->getErrorSummary(true));
            }else{
                //создаем заявку для магазина
                if(!Request::find()->where(['store_id' => $post['User']['store_id']])->exists() && $model->role == 'manager'){
                    $request = new Request();
                    $request->store_id = $post['User']['store_id'];
                    $request->status = Request::REQUEST_INACTIVE;
                    $request->created_at = time();
                    $request->updated_at = time();
                    if($request->save(false)){
                      $store = Agent::find()->where(['id' => $post['User']['store_id']])->one();
                      $store->name = $model->username;
                      $store->update();
                    }
                }
                ShowMessenge('Пользователь успешно создан', 'success');
            }
        }

        return $this->redirect(['index']);
    }
    /**
     * Изменение пользователя
     * принимает get параметр id пользователя
     * и post новые данные пользователя
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = User::findOne($id);
        $stores = Agent::getStoresWithManager();
        $post = Yii::$app->request->post();
        $oldPassword = $model->password;
        if ($model->load($post)) {
            // проверяем на изменения пароля
            if($post['User']['password'] !== $oldPassword){
                $model->auth_key = $model->generateAuthKey();
            }
            $model->save();
            if($model->hasErrors()){
                ShowMessenge($model->getErrorSummary(true));
            }else{
                ShowMessenge('Пользователь успешно изменен', 'success');
            }
            return $this->redirect(['index']);
        }
        return $this->render('update', [
            'model' => $model,
            'stores' => $stores,
        ]);
    }
    /**
     * Удаление пользователя
     * принимает get параметр id пользователя
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $user = User::findOne($id);
        if($user->role != 'admin'){
            $user->delete();
            ShowMessenge('Пользователь успешно удален', 'success');
        }else{
            ShowMessenge('Удаления админа запрещено', 'danger');
        }
        return $this->redirect(['index']);
    }
}