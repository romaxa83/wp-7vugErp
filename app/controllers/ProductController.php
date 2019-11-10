<?php

namespace app\controllers;

use app\service\BazaApi;
use Yii;
use yii\filters\AccessControl;
use app\controllers\AccessController;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\data\ActiveDataProvider;
use app\models\Agent;
use app\models\Category;
use app\models\CsvForm;
use app\models\Product;
use app\models\ProductSearch;
use app\models\VProduct;


class ProductController extends BaseController 
{
    public function behaviors() 
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    AccessController::getAccessRules(Yii::$app->controller->id),
                    [
                        'actions' => ['index','get-product-values','all-product'],
                        'allow' => true,
                        'roles' => ['product_create','product_update']
                    ],
                    [
                        'actions' => ['create','get-data-for-create-variant-product'],
                        'allow' => true,
                        'roles' => ['product_create']
                    ],
                    [
                        'actions' => [
                            'update',
                            'change-category',
                            'change-status',
                            'change-publish-status',
                            'change-view-product',
                            'mass-change-view-product'
                        ],
                        'allow' => true,
                        'roles' => ['product_update']
                    ],
                    [
                        'actions' => ['get-product-list','get-product-values'],
                        'allow' => true,
                        'roles' => ['operation_update','operation_create']
                    ],
                    [
                        'actions' => ['export', 'del-export', 'print-product-pdf', 'print-change-price-pdf'],
                        'allow' => true,
                        'roles' => ['product_print']
                    ]
                ],
            ]
        ];
    }
    
    public function actionUpload() {
        $model = new CsvForm;
        if ($model->load(Yii::$app->request->post())) {

            $file = UploadedFile::getInstance($model, 'file');

            $filename = 'Data.' . $file->extension;
            $upload = $file->saveAs('uploads/' . $filename);
            if ($upload) {
                $csv_file = 'uploads/' . $filename;

                $filecsv = file_get_contents($csv_file);
                debug($filecsv);
                //debug(iconv('windows-1251', 'utf-8', $filecsv));die();
                foreach ($filecsv as $k => $data) {
                    if ($k === 0) {
                        continue;
                    }
                    $temp = explode(";", $data);
                    $temp[2] = stripcslashes($temp[2]);
                    $temp = str_replace(',', '.', $temp);
                    $product = Product::find()->where(['vendor_code' => $temp[1]])->one();

                    if(empty($product)){
                        $product = new Product();
                        $product->vendor_code = $temp[1];
                        $product->name = $temp[2];
                        $product->category_id = $temp[3];
                        $product->agent_id = $temp[4];
                        $product->unit = $temp[6];
                    }
                    $product->amount = $temp[5];
                    $product->start_price = $temp[7];
                    $product->cost_price = $temp[8];
                    $product->trade_price = $temp[9];
                    $product->price1 = $temp[10];
                    $product->price2 = $temp[11];
                    $product->save(false);
                }
                unlink('uploads/' . $filename);
                return $this->redirect(['/']);
            }
        }
    }
    /**
     * Метод создает excel файл.
     * Принимает 2 параметра
     * - тип файла
     * - номер страницы
     * @return json
     */
    public function actionExport() 
    {
        if (Yii::$app->request->isAjax) {
            $type = Yii::$app->request->post('kind');
            $offset = Yii::$app->request->post('offset');
            $countPage = Yii::$app->request->post('countPage');
            $model = new ProductSearch();
            if ($type == 'csv'){
                $model->Csv($offset, $countPage);
            }
            if ($type == 'excel'){
                $model->Excel($offset, $countPage);
            }
            return json_encode(['kind' => $type]);
        }
    }
    /**
     * Метод удаляет временно созданный файл после того как он был отдан юзеру.
     * Принимает один параметр
     * - тип файла
    */
    public function actionDelExport() 
    {
        if (Yii::$app->request->isAjax) {
            $type = Yii::$app->request->post('type');
            if ($type == 'csv'){
                $url = '../web/uploads/product.csv';
            }
            if ($type == 'excel'){
                $url = '../web/uploads/product.xls';
            }
            if(file_exists($url)){
                unlink($url);
            }
        }
    }
    /**
     * Отображения всех моделей product
     * @return mixed 
    */
    public function actionIndex() 
    {
        $model_agent = new Agent();
        $model_csv = new CsvForm();
        $model = new Product();
        $searchModel = new ProductSearch();
        $this->setFilterSession('product', Yii::$app->getRequest()->getQueryParams());
        $dataProvider = $searchModel->search(Yii::$app->session->get('product_session_filter'));

        $totalCount = Product::find()->count();
        $countPage = round($totalCount / getSizePage('prod'));
        $agents_filter = ArrayHelper::map(Agent::getAllAgent(1,true),'firm','firm');
        $agents = ArrayHelper::map(Agent::getAllAgent(1,true),'id','firm');
        $categories = Category::getListCategory();
        $category_form = Category::getVariableForForm();
        // для таблицы менеджера
        $query = Product::find()->where(['view_manager' => 1])->andWhere(['status' => 1]);
        $dataProviderManager = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => !getSizePage('prod') == 0 ? getSizePage('prod') : 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'category_id' => SORT_DESC,
                ]
            ],
        ]);
        return $this->render('index',[
            'agents_filter' => $agents_filter,
            'agents' => $agents,
            'categories' => $categories,
            'category_form' => $category_form,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataProviderManager' => $dataProviderManager,
            'countPage' => $countPage,
            'model_csv' => $model_csv,
            'model_agent' => $model_agent,
            'model' => $model
        ]);
    }
    /**
     * Метод для создания новой модели Product 
     * @return mixed
     * @throws ForbiddenHttpException если у юзера нету доступа.
    */
    public function actionCreate()
    {
        if (Yii::$app->user->can('product_create')){
            $model = new Product();
            $post = Yii::$app->request->post();
            if ($post && $model->load($post)) {
                $model->save();
                if($model->hasErrors()){
                    ShowMessenge($model->getErrorSummary(true),'danger');
                }else{

                    //отсылаем данные для API
                    $dataApi['data'] = $model->getAttributes();
                    $dataApi['requestData'] = $post['Product'];
                    (new BazaApi('product','create'))->add($dataApi);

                    ShowMessenge('Продукт удачно создан','success');
                }
                return $this->redirect(Yii::$app->request->referrer);
            }
            $model_agent = new Agent();
            $category_form = Category::getVariableForForm();
            $categories = Category::getListCategory();
            $agents = ArrayHelper::map(Agent::getAllAgent(1,true),'id','firm');
            return $this->render('create', [
                'category_form' => $category_form,
                'categories' => $categories,
                'model_agent' => $model_agent,
                'model' => $model,
                'agents' => $agents
            ]);
        }else {
            throw new ForbiddenHttpException('У вас нет прав на эти действия');
        }
    }
    /**
     * Метод для обновления модели Product  
     * @param integer $id индефикатор модели
     * @return mixed 
     * @throws ForbiddenHttpException если у юзера нету доступа. 
    */ 
    public function actionUpdate($id) 
    {
        $model = Product::findOne($id);
        $categories = Category::getListCategory();
        $agents = ArrayHelper::map(Agent::getAllAgent(1,true),'id','firm');
        $model->price1 = formatedPriceUA($model->price1);
        $model->price2 = formatedPriceUA($model->price2);
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            $model->status = isset($post['Product']['status']) ? 1 : 0;
            $model->update();
            if($model->hasErrors()){
                ShowMessenge($model->getErrorSummary(true));
            }else{

                //отсылаем данные для API
                $dataApi['data'] = $model->getAttributes();
                $dataApi['requestData'] = $post['Product'];
                (new BazaApi('product','update'))->add($dataApi);

                ShowMessenge('Продукт удачно обновлен','success');
            }
            return $this->redirect('index');
        } else {
            return $this->render('update', [
                'model' => $model,
                'categories' => $categories,
                'agents' => $agents,
            ]);
        }
    }
    /**
     * метод получения категорий с характеристиками и продуктами 
     * принимает 
     * $categoryId - индефикатор модели
     * @return json
    */
    public function actionGetDataForCreateVariantProduct()
    {
        if(Yii::$app->request->isAjax){
            $categoryId = Yii::$app->request->post('category');
            $category = Category::find()->asArray()->where(['id' => $categoryId])->with('chars')->with('charsName')->with('products')->one();
            if(isset($category['charsName'])){ $category['charsName'] = ArrayHelper::map($category['charsName'],'id','name'); } 
            if(empty($category['chars'])){
                return JSON::encode(['type' => 'empty','text' => 'Нет характеристик у выбраной категорий']);
            }else{
                $category['chars'] = ArrayHelper::map($category['chars'],'id','name','id_char');
                return JSON::encode(['type' => 'not empty','product' => $category['products'],'chars' => $category['chars'],'charsName' => $category['charsName']]);
            }
        }
    }
    
    public function actionGetProductList()
    {
        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $query = Product::find()
                    ->select(['id','name as text','amount'])
                    ->asArray()
                    ->where(['!=','status',0])
                    ->limit($post['limit'])
                    ->offset($post['limit'] * ($post['page'] - 1));
            if(!empty($post['category']) || $post['category'] != 0){
                $query->andwhere(['category_id' => $post['category']]);
            }
            if(isset($post['value'])){
                $query->andWhere(['like','name',$post['value']]);
            }
            return JSON::encode(['item' => $query->all(),'totalCount' => $query->count()]);
        }
    }
    
    public function actionAllProduct($name = '') 
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $prods = Product::find()->where(['like','name',$name])->orWhere(['like','vendor_code',$name])->asArray()->all();
        $products = [];
        foreach ($prods as $product) {
            if(!empty($prods)){
                $products['results'][] = ['id' => $product['name'], 'text' => $product['name']];
            }else{
                $products['results'][] = ['id' => 0, 'text' => 'Ничего не найдено'];
            }
        }
        if(empty($products)){
            $products['results'][] = ['text' => 'Ничего не найдено'];
        }
        return $products;
    }
    /*
     * Подтягивает данные выбраного товара,при создании вариативного товара,
     * работает по ajax,получает id-товара,
     * возвращает данные товара
     * @return json
     */
    public function actionGetProductValues()
    {
        if (Yii::$app->request->isAjax) {
            $name = Yii::$app->request->post('name');
            if(is_numeric($name)){
                $product = Product::find()->where(['id' => $name])->asArray()->with('vproducts')->one();
            }else{
                $product = Product::find()->where(['name' => $name])->asArray()->with('vproducts')->one();
            }
            if ($product == null) {
                return JSON::encode(false);
            }else {
                $agentName = Agent::find()->select(['firm'])->where(['id' => $product['agent_id']])->asArray()->one();
                $product['agentName'] = $agentName['firm'];
            }
            foreach ($product['vproducts'] as $key => $one){
                $product['vproducts'][$key]['char_arr'] = unserialize($one['char_value']);
                $product['vproducts'][$key]['char_value'] = VProduct::getCharValueFromId($one['char_value']);
            }
            $product['start_price_uah'] = getConvertUSDinUAH($product['start_price'],getUsd());
            $float = ['usd'=> getFloat('usd'),'uah'=> getFloat('uah')];
            return JSON::encode(['product'=>$product,'float'=>$float]);
        }
    }
    //изменения категорий из life edit каталог товаров
    public function actionChangeCategory()
    {
        if(Yii::$app->request->isAjax){
            $id = Yii::$app->request->post('id');
            $ProductId = Yii::$app->request->post('ProductId');
            $NameCategory = Category::find()->select('name')->where(['id' => $id])->asArray()->one();
            $Product = Product::findOne($ProductId);
            $Product->category_id = $id;
            $Product->update();
            return JSON::encode($NameCategory['name']);
        }
    }
    /**
     * Изменения статуса модели Product работает по Ajax вслучае статус равен 0 то вызываеться actionChangePublishStatus
     * принимает 
     * $id - индефикатор модели 
     * $status - 1/0 (включен/выключен)
     * @throws ForbiddenHttpException если у юзера нету доступа.
    */
    public function actionChangeStatus() 
    {
        if (Yii::$app->user->can('product_delete')) {
            if(Yii::$app->request->isAjax){
                $id = Yii::$app->request->post('id');
                $status = Yii::$app->request->post('status');
                $model = Product::findOne($id);
                $model->status = $status;
                $model->update();

                //отсылаем данные для API
                $dataApi['data'] = $model->getAttributes();
                $dataApi['requestData'] = Yii::$app->request->post();
                (new BazaApi('product','delete'))->add($dataApi);

                ($status == 0) ? $this->actionChangePublishStatus($id,$status) : null;
            }
        } else{
            throw new ForbiddenHttpException(' У вас недостаточно прав для выполнения указаного действия');
        }
    }
    /**
     * Изменения видимости для менеджера модели Product работает по Ajax
     * принимает 
     * $id - индефикатор модели 
     * $status - 1/0 (включен/выключен)
     * @return json 
    */
    public function actionChangeViewProduct()
    {
        if (Yii::$app->request->isAjax){
            $data = Yii::$app->request->post();
            $product = Product::find()->where(['id' => $data['id']])->one();
            $product->view_manager = $data['check'];
            $product->update();
            $res['name'] = $product->name;
            $res['check'] = $data['check'];

            /*****___SEND_TO_API___ ******/
            $dataApi['requestData']['title'] = BazaApi::PRODUCT_TITLE_STATUS_VIEW_MANAGER;
            $dataApi['requestData']['body'] = $data;
            $dataApi['data'] = $product->getAttributes();
            (new BazaApi('product','update'))->add($dataApi);

            return JSON::encode($res);
        }
    }
    /**
     * Изменения видимости для менеджера всех моделей Product на данной странице работает по Ajax
     * принимает 
     * $id - индефикатор модели 
     * $status - 1/0 (включен/выключен)
     * @return json 
    */
    public function actionMassChangeViewProduct()
    {
        if (Yii::$app->request->isAjax){
            $data = Yii::$app->request->post();
            foreach ($data['data'] as $id){
                $product = Product::find()->where(['id' => $id])->one();
                $product->view_manager = $data['check'];
                $product->update();

                /*****___SEND_TO_API___ ******/
                $dataApi['requestData']['title'] = BazaApi::PRODUCT_TITLE_STATUS_VIEW_MANAGER;
                $dataApi['requestData']['body'] = $data;
                $dataApi['data'] = $product->getAttributes();
                (new BazaApi('product','update'))->add($dataApi);
            }
            $res['check'] = $data['check'];
            return JSON::encode($res);
        }
    }
    /**
     * Изменения статус для магазина модели Product работает по Ajax
     * так же отправляет данные на магазин просто выгодно
     * принимает 
     * $id - индефикатор модели 
     * $status - 1/0 (включен/выключен)
    */
    public function actionChangePublishStatus($id = null,$status = null)
    {
        $data = Yii::$app->request->post();
        $product = Product::findOne(($id == null) ? $data['id'] : $id);

        if($data['status'] == Product::STATUS_SHOP_UNACTIVE && $product->publish_status == Product::STATUS_SHOP_ACTIVE){
            $product->publish_status = Product::STATUS_SHOP_DRAFT;
        } else {
            $product->publish_status = ($status == null) ? $data['status'] : $status;
        }
        $product->update();
        if($product->publish_status !== Product::STATUS_SHOP_ACTIVE && $product->is_variant == 2){
            $v_products = VProduct::find()->where(['product_id'=>$product->id])->andWhere(['publish_status'=>1])->all();
            if(count($v_products)){
                foreach ($v_products as $v_product){
                    $v_product->publish_status = 0;
                    $v_product->update();
                }
            }
        }
    }

    public function actionPrintProductPdf() 
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', -1);
        $product = Product::find()->asArray()->with('category')->with('agent')->all();
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $product,
            'pagination' => false
        ]);
        $view = $this->renderPartial('print_pdf_product', ['dataProvider' => $dataProvider]);
        $model = new \app\models\Operations();
        $pdf = $model->getPdf($view, 1);
        return @$pdf->render();
    }
    
    public function actionPrintChangePricePdf() 
    {
        $query = Product::find()->where(['>', 'change_price', 0]);
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => FALSE,
            'sort' => FALSE,
        ]);
        if ($dataProvider->getTotalCount() == 0) 
            return 'Нет данных';
        $view = $this->renderPartial('print_pdf_change_price', ['dataProvider' => $dataProvider]);
        $model = new \app\models\Operations();
        $pdf = $model->getPdf($view, 1);
        $output = @$pdf->render();
        Yii::$app->db->createCommand('UPDATE product SET change_price = (change_price - 1) WHERE change_price > 0')->execute();
        return $output;
    }    
}