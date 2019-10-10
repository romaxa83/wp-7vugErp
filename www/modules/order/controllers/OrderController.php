<?php

namespace app\modules\order\controllers;

use Yii;
use app\controllers\BaseController;
use app\modules\order\models\Order;
use app\modules\order\models\OrderSearch;
use app\modules\order\models\OrderProductSearch;
use yii\filters\AccessControl;
use app\controllers\AccessController;

class OrderController extends BaseController
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
                        'roles' => ['admin']
                    ]
                ]
            ]
        ];
    }

    public function actionIndex() {
        $searchModel = new OrderSearch();
        $this->setFilterSession('order', Yii::$app->getRequest()->getQueryParams());
        $dataProvider = $searchModel->search(Yii::$app->session->get('order_session_filter'));
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider
        ]);
    }

    public function actionView() {
        $id = Yii::$app->request->get('id');
        $searchModel = new OrderProductSearch();
        $this->setFilterSession('order_product', Yii::$app->getRequest()->getQueryParams());
        $dataProvider = $searchModel->search(Yii::$app->session->get('order_product_session_filter'), $id);
        return $this->render('view', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider
        ]);
    }

    public function actionUpdate() {
        $id = Yii::$app->request->get('id');
    }

    public function actionDelete() {
        $id = Yii::$app->request->get('id');
        Order::deleteAll(['order' => $id]);
        $this->redirect(['index']);
    }

}
