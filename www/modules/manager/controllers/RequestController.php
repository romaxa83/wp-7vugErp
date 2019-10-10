<?php

namespace app\modules\manager\controllers;

use Yii;
use app\models\Product;
use app\modules\manager\models\RequestProduct;
use app\controllers\BaseController;
use yii\filters\AccessControl;
use app\controllers\AccessController;
use yii\helpers\Json;
use app\models\Category;
use app\service\BazaApi;

class RequestController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    AccessController::getAccessRules(Yii::$app->controller->id),
                    [
                        'actions' => ['get-product-list'],  
                        'allow' => true,
                        'roles' => ['manager']
                    ],
                    [
                        'actions' => ['change-amount-product'],
                        'allow' => true,
                        'roles' => ['manager', 'admin']
                    ]
                ]
            ]
        ];
    }
    /**
     * Метод для поиска товара
    */
    public function actionGetProductList()
    {
        $this->checkAuthKey();
        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $typePrice = Yii::$app->session->get('getStorePrice');
            $query = Product::find()
                    ->select(['id','name as text','amount',"price{$typePrice} as price",'is_variant'])
                    ->asArray()
                    ->where(['!=','status',0])
                    ->andWhere(['view_manager' => 1])
                    ->andWhere(['like','name',$post['value'] ?? ''])
                    ->orderBy(['name' => SORT_ASC]);
            if(!empty($post['category']) || $post['category'] != 0){
                $category = Category::GetIdChild(Category::addItem(Category::find()->where(['status' => 1])->asArray()->all(), $post['category']), $post['category']);
                $arr = explode(',',$category);
                $arr[count($arr) - 1] = $post['category'];
                $query->andwhere(['in','category_id',$arr]);
            }
            return JSON::encode($this->formatPrice($query->all()));
        }
    }    
    /**
     *   Метод меняет в заявке кол-во товара при редактировании
    */
    public function actionChangeAmountProduct($data = null)
    {
        $this->checkAuthKey();
        $post = ($data === null) ? Yii::$app->request->post() : $data; 
        $result = RequestProduct::ChangeAmount($post['product_id'],$post['request_id'],abs($post['amount']));
        if($result['status']){

            /*****___SEND_TO_API___ ******/
            $dataApi['requestData']['title'] = BazaApi::REQUEST_TITLE_MANAGER_EDIT_PRODUCT;
            $dataApi['requestData']['body'] = $post;
            $dataApi['data'] = $result['model']->getAttributes();
            (new BazaApi('request-product','update'))->add($dataApi);

            return Json::encode(['status' => true]);
        }else{
            return Json::encode(['status' => false, FormattedMessenge($result['model']->getErrorSummary(true))]);
        }
    }
    
    private function formatPrice($array) : array
    {
        return array_map(function($item){
            $item['price'] = formatedPriceUA($item['price']);
            return $item;
        },$array);
    }
}