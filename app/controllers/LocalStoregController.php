<?php
namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use app\models\Archive;
use app\models\Product;
use app\models\Category;
use app\models\OperComing;
use app\models\OperConsumption;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

class LocalStoregController extends BaseController 
{
    /*
     * Разпределения действий по контроллерам 
     * params : 
     * string $post['action'] -> роут 
     * string $post['data'] -> строка данных переданых ajax которые имел в ответе error
     */
    public function actionCheckErrorAjax()
    {
        if(Yii::$app->request->isAjax){ 
            $post = Yii::$app->request->post();
            $route = explode('/',$post['oldUrl']);
            $data = $this->actionPreparedData($post['data']);
            switch (true){
                case $route[1] === 'product' : 
                    $this->actionProduct($route[2],$data); 
                break;
                case $route[1] === 'operation' : 
                    $this->actionTransaction($route[2],$data); 
                break;
                case $route[1] === 'product-request' : 
                    $this->actionProductRequest($route[2],$data); 
                break;
                case $route[1] === 'category' : 
                    $this->actionCategory($route[2],$data); 
                break;
            }
        }
    }
    /*
     * Применения действий которые имели в ответе error для контроллера Product
     * params : 
     * string $action -> действия 
     * array $data -> массив данных переданых ajax которые имел в ответе error
     */
    private function actionProduct($action,$data)
    {        
        if($action === 'change-status' || $action === 'change-publish-status' || $action === 'change-view-product'){
            $prod = Product::findOne($data['id']);
            $this->actionChangeStatus($data, $prod, $action);
        }
    }
    /*
     * Применения действий которые имели в ответе error для контроллера Operations
     * params : 
     * string $action -> действия 
     * array $data -> массив данных переданых ajax которые имел в ответе error
     */
    private function actionTransaction($action,$data)
    {
        $controller = new OperationController('operation', \yii\base\Module::className());
        if($action === 'send-in-archive' || 'send-in-archive-value'){
            $this->Archive($data,$controller);
        }
        if($action === 'массовый в архив'){
            //массовый в архив
        }
    }
    
    private function Archive($data,$controller)
    {
        $model = Archive::find()->where(['transaction_id' => $data['id']])->one();
        if(empty($model)){
            $id = Json::decode($controller->actionSendInArchive($data));
            $controller->actionSendInArchiveValue(['id' => ['transaction_id' => $data['id'],'archive_id' => $id]]);
        }else{
            $productArchive = ArrayHelper::index($model->products,function($item){$this->sortProduct($item);});
            $productTransaction = ArrayHelper::index(OperComing::find()->where(['transaction_id' => $data['id']])->all(),function($item){$this->sortProduct($item);});
            if(!empty($productArchive)){
                foreach($productTransaction as $key => $one){
                    if(isset($productArchive[$key])){
                        $id[] = $one->id;
                    }
                }
                OperComing::deleteAll(['id' => $id]);
            }
            $controller->actionSendInArchiveValue(['id' => ['transaction_id' => $data['id'],'archive_id' => $model->id]]);
        }
    }
    /*
     * Применения действий которые имели в ответе error для контроллера ProductRequest
     * params : 
     * string $action -> действия 
     * array $data -> массив данных переданых ajax которые имел в ответе error
     */
    private function actionProductRequest($action,$data){
        $cntr = new ProductRequestController('product-request', \yii\base\Module::className());
        (isset($data['store_id'])) ? $request = ProductRequest::find()->select('prod_value')->where(['store_id' => $data['store_id']])->asArray()->one() : null; 
        if($action === 'add-product-request'){
            $prod_value = ProductRequest::FormatProdValueProdRequest($request['prod_value']);
            $product = array_column($prod_value, 'prod_name', 'product');
            if(!isset($product['p'.$data['prod_id']])){
                $cntr->actionAddProductRequest($data);
            }
        }
    }
    /*
     * Применения действий которые имели в ответе error для контроллера Category
     * params : 
     * string $action -> действия 
     * array $data -> массив данных переданых ajax которые имел в ответе error
     */
    private function actionCategory($action,$data)
    {
        if($action === 'change-status' || $action === 'change-publish-status'){
            $category = Category::findOne($data['id']);
            $this->actionChangeStatus($data, $category, $action);
        }
    }
    /*
     * логика изменения статуса для сущностей у которых присутствует статус публикаций 
     * для магазина 
     * params : 
     * array $data['status'] -> 1/0 обезательный параметр 
     * obj $model -> обьект 
     * string $action -> действия 
     */
    private function actionChangeStatus($data,$model,$action)
    {
        if($action !== 'change-view-product'){
            if($data['status'] == 0){
                $model->status = $data['status'];
                $model->publish_status = $data['status'];
            }else{
                if($action === 'change-status'){
                    $model->status = $data['status'];
                }else{
                    $model->publish_status = $data['status'];
                }
            }
        }else{
            $model->view_manager = $data['check'];
        }
        $model->update();
    }
    /*
     * распарсивания url подобной строки 
     * params : string $data ->  url подобнпя строка
     * return : mixed $outPut   
     */
    private function actionPreparedData($data)
    {
        $outPut = null;
        parse_str($data,$outPut);
        return $outPut;
    }
    
    private function sortProduct($item)
    {
        return empty($item->vproduct_id) ? $item->product_id : $item->vproduct_id.'v';
    }
}