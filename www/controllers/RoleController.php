<?php

namespace app\controllers;

use app\models\AuthAssignment;
use Yii;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\rbac\Role;
use yii\rbac\Permission;
use yii\helpers\ArrayHelper;
use yii\validators\RegularExpressionValidator;
use app\controllers\AccessController;
use app\controllers\BaseController;

class RoleController extends BaseController {

    protected $error;
    protected $pattern4Role = '/^[a-zA-Z0-9_-]+$/';
    protected $pattern4Permission = '/^[a-zA-Z0-9_\/-]+$/';
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
                        'actions' => ['create', 'update', 'delete', 'permission'],
                        'roles' => ['user_add_view']
                    ]
                ],
            ],
        ];
    }
    /**
     * Добавление новой роли
     * @return mixed
    */
    public function actionCreate() 
    {
        $post = Yii::$app->request->post('AuthItem');
        if ($post['name'] && $this->validate($post['name'], $this->pattern4Role) && $this->isUnique($post['name'])) {
            $role = Yii::$app->authManager->createRole($post['name']);
            $role->description = $post['description'];
            Yii::$app->authManager->add($role);
            ShowMessenge('Роль успешно добавленна', 'success');
        } else {
            ShowMessenge($this->error);
        }

        return $this->redirect('/user/index');
    }
    /**
     * Изменение роли
     * принимает get параметр имя роли
     * и post новые данные роли
     * @param string $name имя роли
     * @return mixed
     * @throws BadRequestHttpException
    */
    public function actionUpdate($name) 
    {
        if($name == 'admin'){
            ShowMessenge('Редактирования admin роли запрещено');
            return $this->redirect('/user/index');
        }
        $role = Yii::$app->authManager->getRole($name);
        if (Yii::$app->request->post('name') && $this->validate(Yii::$app->request->post('name'), $this->pattern4Role)) {
            if (Yii::$app->request->post('name') != $name && !$this->isUnique(Yii::$app->request->post('name'))) {
                return $this->render('update', [
                    'role' => $role,
                    'error' => $this->error
                ]);
            }
            $role = $this->setAttribute($role, Yii::$app->request->post());
            Yii::$app->authManager->update($name, $role);
            return $this->redirect('/user/index');
        }
        return $this->render('update', [
            'role' => $role,
            'error' => $this->error
        ]);
    }
    /**
     * Удаление роли
     * принимает get параметр имя роли
     * @param string $name имя роли
     * @return mixed
    */
    public function actionDelete($name) 
    {
        if ($name == 'admin') {
            ShowMessenge('Роль админ удалять запрещено');
            return $this->redirect('/user/index');
        }
        $result = AuthAssignment::find()->where(['item_name' => $name])->all();
        if (count($result) > 0) {
            ShowMessenge('К роли привязаны пользователи');
            return $this->redirect('/user/index');
        }
        $role = Yii::$app->authManager->getRole($name);
        if ($role) {
            Yii::$app->authManager->removeChildren($role);
            Yii::$app->authManager->remove($role);
        }
        ShowMessenge('Роль успешно удалена', 'success');
        return $this->redirect('/user/index');
    }
    /**
     * Установка доступов для ролей
     * @return mixed
    */
    public function actionPermission() 
    {
        if(Yii::$app->request->post()){
            $roles = ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'description');
            $permissions = ArrayHelper::map(Yii::$app->authManager->getPermissions(), 'name', 'description');
            foreach ($roles as $role => $desc) {
                $post = Yii::$app->request->post($role);
                $role = Yii::$app->authManager->getRole($role);
                foreach ($permissions as $permit => $description) {
                    $permission = Yii::$app->authManager->getPermission($permit);
                    if($post && in_array($permit, $post)){
                        if(!Yii::$app->authManager->hasChild($role, $permission)){
                            Yii::$app->authManager->addChild($role, $permission);
                        }
                    }elseif(Yii::$app->authManager->hasChild($role, $permission)){
                        Yii::$app->authManager->removeChild($role, $permission);
                    }
                }
            }
            ShowMessenge('Доступы успешно обновленны', 'success');
        }
        return $this->redirect('/user/index');
    }
    /**
     * Установка атрибутов роли
     * @param object $object объэкт роли
     * @param array $data массив с именем и описанием
     * @return object $object объэкт роли
    */
    protected function setAttribute($object, $data) 
    {
        $object->name = $data['name'];
        $object->description = $data['description'];
        return $object;
    }
    /**
     * Валидация значения по регулярному выражению
     * принимает значение и регулярное выражение
     * @param string $field значения
     * @param string $regex регулярное выражение
     * @return bool
    */
    protected function validate($field, $regex)
    {
        $validator = new RegularExpressionValidator(['pattern' => $regex]);
        if ($validator->validate($field)) {
            return true;
        } else {
            $this->error[] = 'Значение ' . $field . ' содержит недопустимые символы';
            return false;
        }
    }
    /**
     * Проверка именни на уникальность среди ролей и разрешений
     * @param string $name имя роли
     * @return bool
    */
    protected function isUnique($name) 
    {
        $role = Yii::$app->authManager->getRole($name);
        $permission = Yii::$app->authManager->getPermission($name);
        if ($permission instanceof Permission) {
            $this->error[] = 'Разрешение с таким именем уже существует' . ': ' . $name;
            return false;
        }
        if ($role instanceof Role) {
            $this->error[] = 'Роль с таким именем уже существует' . ': ' . $name;
            return false;
        }
        return true;
    }
}
