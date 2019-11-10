<?php

namespace app\controllers;

use Yii;
use app\models\Category;
use yii\helpers\Json;
use app\controllers\BaseController;
use app\controllers\AccessController;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class CategoryController extends BaseController
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
                        'actions' => ['get-list-category'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['category_create', 'category_update'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['category_create'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'change-status', 'change-publish-status'],
                        'roles' => ['category_update'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
            ]
        ];
    }
    /**
     * Метод отображения всех моделей Category 
     * @return mixed
    */
    public function actionIndex()
    {
        $model = new Category();
        $model->position = 0;
        $category = Category::find()->orderBy('position')->asArray()->all();
        $cats = Category::addItem($category, 0);
        $parents = Category::getListCategory();
        $chars = $model->arrCharacteristic;
        return $this->render('index', [
            'cats' => $cats,
            'model' => $model,
            'chars' => $chars,
            'parent_cats' => $parents
        ]);
    }   
    /**
     * Создания новой модели Category
     * @param integer $id Служит индефикатором родителской категорий
     * @return mixed
     * @throws ForbiddenHttpException если у юзера нету доступа.
    */
    public function actionCreate($id = null)
    {
        $post = Yii::$app->request->post();
        $model = new Category();
        if(Yii::$app->request->isAjax){
            return $this->actionCreateFromAjax($model, $post);
        }
        $parent_cats = Category::getListCategory();
        $chars = $model->arrCharacteristic;
        $model->parent_id = $id;
        if(!isset($parent_cats[$id]) && ($model->parent_id != 0 || $model->parent_id != null)){
            ShowMessenge('При выключеной родительской категорий , создания под категорий запрещено','danger');
            return $this->redirect('/category/index');
        }
        if ($model->load($post)) {
            if($model->saveCategory($post['Category'])){
                ShowMessenge($model->getErrorSummary(true));
            }else{
                ShowMessenge('Категория удачно создана','success');
            }
            return $this->redirect('/category/index');
        } else {
            $model->position = 0;
            return $this->render('create', [
                'model_cat' => $model,
                'parent_cats' => $parent_cats,
                'chars_cat' => $chars,
            ]);
        }
    }
    /**
     * Всопогательный метод для создания модели Category так же генерирует список категорий в ответе либо ошибку 
     * @param object $model модель Category
     * @param array $data данные присланые ajax (position,name,parent_id,chars - список характеристик)
     * @return json
    */
    private function actionCreateFromAjax($model,$data)
    {
        if($model->load($data) && $model->saveCategory($data['Category'])){
            $error = FormattedMessenge($model->getErrorSummary(true),'danger',true);
            return JSON::encode(['type'  => 'error','msg' => $error]);
        }
        return JSON::encode(['type'  => 'success','data' => $model]);   
    }
    /**
     * Метод обновления модели Category
     * @param integer $id Индефикатор модели 
     * @return mixed 
     * @throws ForbiddenHttpException если у юзера нету доступа.
    */
    public function actionUpdate($id)
    {
        $model = Category::findOne($id);
        $parent_cats = Category::getListCategory();
        $chars = $model->getArrCharacteristic();
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            if($model->saveCategory($post['Category'])){
                ShowMessenge($model->getErrorSummary(true));
            }else{
                ShowMessenge('Категория удачно обновлена','success');
            }
            return $this->redirect('/category/index');
        }else {
            return $this->render('update', [
                'model' => $model,
                'parent_cats' => $parent_cats,
                'chars' => $chars
            ]);
        }
    }
    /**
     * 
    */
    public function actionGetListCategory($placeholderOption = false)
    {
        if(Yii::$app->request->isAjax){
            $Category = Category::getListCategory(true);
            if($placeholderOption){
                $response[] = ['id' => 0,'text' => 'Всё категории','level' => 1,'group' => 0];
            }else{
                $response = [];
            }
            foreach($Category as $key => $value){
                $LevelAndName = explode('|', $value);
                $CategoryWithLevel['id'] = $key;
                $CategoryWithLevel['text'] = (isset($LevelAndName[1])) ? $LevelAndName[1] : $LevelAndName[0];
                if(isset($LevelAndName[1])){
                    $CategoryWithLevel['level'] = (int)$LevelAndName[0];
                    $CategoryWithLevel['group'] = $group;
                }else{
                    $group = $key;
                    $CategoryWithLevel['level'] = 1;
                    $CategoryWithLevel['group'] = $group;
                }
                $CategoryWithLevel['hasChild'] = substr(next($Category),0,1) > $CategoryWithLevel['level'];
                $response[] = $CategoryWithLevel;
            }
            return json_encode($response);
        }
    }
    /**
     * Изменения статуса модели Category работает по Ajax вслучае статус равен 0 то вызываеться RecursiveChangeStatus
     * принимает 
     * $id - индефикатор модели 
     * $status - 1/0 (включен/выключен)
     * @throws ForbiddenHttpException если у юзера нету доступа.
    */
    public function actionChangeStatus()
    {
        if(Yii::$app->request->isAjax){
            $id = Yii::$app->request->post('id');
            $status = Yii::$app->request->post('status');
            $ArrayId = $this->GetChangeArrayId($id,$status);
            Category::updateAll(['status' => $status],['id' => $ArrayId]);
            return JSON::encode($ArrayId);
        }
    }
    /*
     * при изменения статуса в выкл вернуть ключ всех категорий ниже в иерархия 
     * при изменения статуса в вкл вернуть ключ всех категорий высше в иерархия 
    */
    private function GetChangeArrayId($CategoryId = null,$CategoryStatus = null)
    {
        //получения данных (ключ записи,статус,тип статуса,списка категорий)
        $CategoryList = Category::find()->asArray()->indexBy('id')->all();
        //расприделения на поднятия ниже и высше в иерархий категорий
        if($CategoryList[$CategoryId]['parent_id'] != 0 && $CategoryStatus){
            $ArrayId = explode(',',substr(Category::GetIdParent($CategoryList,$CategoryId),0,-1));
        }elseif(!$CategoryStatus){
            $FormattedCategoryList = Category::addItem($CategoryList, $CategoryId);
            $ArrayId = explode(',',substr(Category::GetIdChild($FormattedCategoryList,$CategoryId),0,-1));
        }
        if(!isset($ArrayId) || !in_array($CategoryId,$ArrayId) ){ 
            $ArrayId[] = $CategoryId; 
        }
        return $ArrayId;
    }
    /**
     * Метод изменяет статус для магазина 
     * @param integer $id индефикатор модели 
     * @param status $status 1/0 (включен/выключен)
    */
    public function actionChangePublishStatus()
    {
        if(Yii::$app->request->isAjax){
            $id = Yii::$app->request->post('id');
            $status = Yii::$app->request->post('status');
            $ArrayId = $this->GetChangeArrayId($id,$status);
            Category::updateAll(['publish_status' => $status],['id' => $ArrayId]);
            return JSON::encode($ArrayId);
        }
    }
}
