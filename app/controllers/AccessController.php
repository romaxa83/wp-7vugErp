<?php

namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use app\models\AccessSearch;
use app\models\Access;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

class AccessController extends BaseController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ]
                ]
            ]
        ];
    }

    public function actionIndex()
    {
        $searchModel = new AccessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('access', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    static function getAccessRules($controller)
    {
        $actions = Access::find()->select('action')->asArray()->where(['controller' => $controller, 'status' => 1])->all();
        if (count($actions) > 0) {
            $actions = ArrayHelper::getColumn($actions, 'action');
        } else {
            $actions = ['special-plug'];
        }
        return [
            'actions' => $actions,
            'allow' => false,
            'denyCallback' => function () {
                $_SESSION['warning'] = json_encode([
                    'title' => 'Доступ закрыт',
                    'description' => 'Доступ закрыт',
                    'type' => 'danger'
                ]);
                Yii::$app->response->redirect('/');
            }];
    }

    public function actionSetStatus()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post();
            $access = Access::findOne($data['id']);
            $access->status = (int) $data['value'];
            $access->update();
        }
    }

    public function actionAddAccess()
    {
        $model = new Access();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->redirect(['/access']);
        }
        return $this->render('create', [
                    'model' => $model,
                    'data' => $this->getControllersAndActions()
        ]);
    }

    public function actionGetActions()
    {
        if (Yii::$app->request->isAjax) {
            $post = Yii::$app->request->post();
            $data = $this->getControllersAndActions();
            return json_encode($data[$post['controller']]);
        }
    }

    public function actionUpdate($id)
    {
        $model = Access::findOne($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->redirect(['/access']);
        }
        return $this->render('create', [
                    'model' => $model,
                    'data' => $this->getControllersAndActions()
        ]);
    }

    public function actionDelete($id)
    {
        $access = Access::findOne($id);
        $access->delete();
        $this->redirect(['/access']);
    }

    protected function getControllersAndActions()
    {
        $controllerlist = [];
        if ($handle = opendir('../controllers')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && substr($file, strrpos($file, '.') - 10) == 'Controller.php') {
                    $controllerlist[] = $file;
                }
            }
            closedir($handle);
        }
        asort($controllerlist);
        $fulllist = [];
        foreach ($controllerlist as $controller) {
            $handle = fopen('../controllers/' . $controller, "r");
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    if (preg_match('/public function action(.*?)\(/', $line, $display)) {
                        if (strlen($display[1]) > 2) {
                            //$fulllist[substr($controller, 0, -4)][] = strtolower($display[1]);
                            $fulllist[substr($controller, 0, -4)][] = strtolower(preg_replace("/(?!^)([A-Z])/", '-$1', $display[1]));
                        }
                    }
                }
            }
            fclose($handle);
        }
        return $fulllist;
    }

}
