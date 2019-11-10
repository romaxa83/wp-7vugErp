<?php

namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use app\models\Operations;
use app\models\OperAdjustment;
use app\models\Category;
use app\models\Product;
use app\models\VProduct;
use app\controllers\AccessController;
use yii\filters\AccessControl;
use yii\helpers\Json;
use app\modules\logger\service\LogService;
use app\service\BazaApi;

class OperationAdjustmentController extends BaseController 
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
                        'actions' => ['index', 'save-change'],
                        'roles' => ['operation_create'],
                    ],
                ],
            ]
        ];
    }
    /**
     * Отображения стартовой страницы корректировки 
     * @return mixed 
     */
    public function actionIndex() 
    {
        $category = Category::getListCategory();
        $product = new Product();
        return $this->render('adjustment', compact('category', 'product'));
    }
    /**
     * Метод сохранения изменения корректировки 
     */
    public function actionSaveChange() 
    {
        $post = Yii::$app->request->post();
        if (!empty($post['id'])) {
            $error = [];
            foreach ($post['id'] as $key => $id) {
                $product = Product::findOne($id);
                ($post['amount']['p' . $id] != '') ? $product->amount = $post['amount']['p' . $id] : $error[$product->id] = 'Количество'; 
                ($post['start_price'][$key] != '') ? $product->start_price = $post['start_price'][$key] : $error[$product->id] = 'Цена прихода'; 
                ($post['cost_price'][$key] != '') ? $product->cost_price = $post['cost_price'][$key] : $error[$product->id] = 'Себестоимость';
                ($post['trade_price'][$key] != '') ? $product->trade_price = $post['trade_price'][$key] : $error[$product->id] = 'Оптовая цена';
                if(isset($error[$product->id])){
                    return Json::encode(['status' => false,'error' => $error]);
                }
                $product->date_adjustment = date("Y-m-d H:i:s");
                
                LogService::logModel($product, 'update');
                
                $product->update();
                if (isset($post['amount'][$id])) {
                    foreach ($post['amount'][$id] as $key_variant => $oneVariant) {
                        $variant_product = VProduct::findOne($key_variant);
                        ($oneVariant) ? $variant_product->amount = $oneVariant : $error[$key] = 'Количество';
                        $variant_product->save();
                    }
                }
            }
            $this->AdjustmentTransaction($post);
            return Json::encode(['status' => true,'error' => $error]);
        }
    }
    /**
     * Метод создания транзакций корректировки 
     * @return mixed
     */
    private function AdjustmentTransaction($new) 
    {
        if (count($new) > 0) {
            $model_Operations = new Operations();
            $model_Operations->date = date("Y-m-d H:i:s");
            $model_Operations->status = 2;
            $model_Operations->type = 3;
            $model_Operations->where = 1;
            $model_Operations->whence = 1;
            $model_Operations->save();
            foreach ($new['id'] as $key => $id) {
                if (isset($new['amount'][$id])) {
                    foreach ($new['amount'][$id] as $keyVariant => $oneVariant) {
                        OperAdjustment::saveRow([
                            'amount' => $oneVariant,
                            'cost_price' => $new['cost_price'][$key],
                            'start_price' => $new['start_price'][$key],
                            'trade_price' => $new['trade_price'][$key],
                            'transaction_id' => $model_Operations->id,
                            'product_id' => $id,
                            'vproduct_id' => $keyVariant
                        ]);
                    }
                } else {
                    OperAdjustment::saveRow([
                        'amount' => $new['amount']['p' . $id],
                        'cost_price' => $new['cost_price'][$key],
                        'start_price' => $new['start_price'][$key],
                        'trade_price' => $new['trade_price'][$key],
                        'transaction_id' => $model_Operations->id,
                        'product_id' => $id
                    ]);
                }
                $productApi[] = Product::find()->where(['id' => $id])->asArray()->one();
            }
            $model_Operations->saveTotalValue();

            /*****___SEND_TO_API___ ******/
            $dataApi['requestData']['title'] = BazaApi::TRANSACTION_TITLE_ADJUSTMENT;
            $dataApi['requestData']['body'] = $new;
            $dataApi['data']['transaction'] = $model_Operations->getAttributes();
            $dataApi['data']['product'] = $productApi;
            (new BazaApi('transaction','create'))->add($dataApi);
        }
    }
}