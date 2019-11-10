<?php

namespace app\modules\logger\controllers;

use Yii;
use app\modules\logger\models\ActivityLog;
use app\modules\logger\models\ActivityLogSearch;
use app\modules\logger\models\ActivityLogViewModel;
use app\controllers\BaseController;
use yii\helpers\Json;
use yii\filters\AccessControl;
use app\controllers\AccessController;

class DefaultController extends BaseController 
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

    public function actionIndex() 
    {
        ActivityLogViewModel::setModule($this->module);
        $totalCount = ActivityLog::find()->count();
        $countPage = round($totalCount / 100);
        $searchModel = new ActivityLogSearch();
        $searchModel->setEntityMap($this->module->entityMap);
        $this->setFilterSession('logger', Yii::$app->getRequest()->getQueryParams());
        $dataProvider = $searchModel->search(Yii::$app->session->get('logger_session_filter'));
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'countPage' => $countPage,
        ]);
    }
    /**
     * Метод проверяет результат архиваций после очищает таблицу
    */
    public function actionClear() 
    {
        $session = Yii::$app->session;
        $group = $session->get('group');
        $key = Yii::$app->request->post('key');
        if(file_exists('uploads/log-archive')){
            $year = substr($group[$key]['created_at'], 0, -6);
            $month = substr($group[$key]['created_at'], 5, -3);
            $day = substr($group[$key]['created_at'], 8);
            $filename = "uploads/log-archive/" . $year . '/' . $month . '/' . $day . '.csv';
            if(!file_exists("uploads/log-archive/" . $year)){
                $result = false;
            }else{
                if(!file_exists("uploads/log-archive/" . $year . '/' . $month)){
                    $result = false;
                }else{
                    if(file_exists($filename)){
                        $result = $this->checkFile($filename, $group[$key]);
                    }
                }
            }
        }
        if($result == false){
            $_SESSION['ErrorFile'][$key] = $filename;
            return 'error';
        }else{
            if(isset($group[$key + 1])){
                ActivityLog::deleteAll('id < ' . $group[$key + 1]['id']);
                $key++;
                return JSON::encode(['key' => $key]);
            }else{
                ActivityLog::deleteAll();
                $session->remove('group');
                return 'stop';
            }
        }
    }
    /**
     * Метод записует группы логов по д-м-г
    */
    public function actionArchive() 
    {
        if(Yii::$app->request->isAjax){
            if(!file_exists("uploads/log-archive/")){
                mkdir('uploads/log-archive/', 0777);
                chown('uploads/log-archive/', 'app-data:app-data');
            }
            $session = Yii::$app->session;
            $group = ActivityLog::find()->select(['id', 'created_at'])->groupBy(["MONTH(FROM_UNIXTIME(created_at)) , YEAR(FROM_UNIXTIME(created_at)) , DAY(FROM_UNIXTIME(created_at))"])->asArray()->all();
            foreach($group as $key => $one){
                if(isset($group[$key + 1])){
                    $next = $group[$key + 1]['id'];
                    $count = ActivityLog::find()->where(['>=', 'id', $one['id']])->andWhere(['<', 'id', $next])->count();
                }else{
                    $count = ActivityLog::find()->where(['>=', 'id', $one['id']])->count();
                }
                $group[$key]['count'] = $count;
                $group[$key]['countPage'] = ceil($count / 100);
                $group[$key]['countPage'] == 0 ? $group[$key]['countPage'] = 1 : null;
                $group[$key]['created_at'] = gmdate("Y-m-d", $one['created_at']);
            }
            if(empty($group)){
                return false;
            }else{
                $session->set('group', $group);
                return true;
            }
        }
    }
    /**
     * Метод вытягивает с сессий группы логов делает запросы к бд и 
     * передает на метод модели который делает запись в файл
    */
    public function actionGetData() 
    {
        if(Yii::$app->request->isAjax){
            $group = Yii::$app->session->get('group');
            $Export = new ActivityLogSearch();
            $key = Yii::$app->request->post('key');
            $i = Yii::$app->request->post('index');
            if(isset($group[$key + 1])){
                $next = $group[$key + 1]['id'];
                $models = ActivityLog::find()->where(['>=', 'id', $group[$key]['id']])->andWhere(['<', 'id', $next])->asArray()->limit(100)->offset($i * 100)->all();
            }else{
                $models = ActivityLog::find()->where(['>=', 'id', $group[$key]['id']])->asArray()->limit(100)->offset($i * 100)->all();
            }
            if(empty($models)){
                $key++;
                $i = 0;
            }else{
                $Export->Csv($models, $i, $group[$key]['countPage'], $key);
                $i++;
            }
            $last = count($group) - 1;
            $countAction = ($key == $last) ? $group[$key]['countPage'] : $group[$key]['countPage'] + 1;
            return JSON::encode(['key' => $key, 'index' => $i, 'countAction' => $countAction]);
        }
    }
    /**
     * Метод рендерит вид архива логов
    */
    public function actionOutPut() 
    {
        $year = scandir('uploads/log-archive/');
        unset($year[0], $year[1], $year[2]);
        return $this->render('view_archive', compact('year'));
    }
    /**
     * Метод подгружает месяцы
    */
    public function actionGetMonth() 
    {
        if (Yii::$app->request->isAjax) {
            $year = Yii::$app->request->post('year');
            $month = scandir('uploads/log-archive/' . $year . '/');
            unset($month[0], $month[1]);
            return JSON::encode(['view' => $this->renderAjax('view_month', compact('year', 'month')), 'year' => $year]);
        }
    }
    /**
     * Метод подгружает дни
    */
    public function actionGetDay() 
    {
        if (Yii::$app->request->isAjax) {
            $year = Yii::$app->request->post('year');
            $month = Yii::$app->request->post('month');
            $day = scandir('uploads/log-archive/' . $year . '/' . $month . '/');
            unset($day[0], $day[1]);
            return JSON::encode(['view' => $this->renderAjax('view_day', compact('day', 'year', 'month')), 'year' => $year]);
        }
    }
    /**
     * Метод проверяет файл (первый id равен ли Group['id'], количество записей равно ли оно Group['count']) 
    */
    protected function checkFile($filename, $group) 
    {
        $file = file($filename);
        $result = false;
        if (isset($group['Beforcount'])) {
            $file = array_reverse($file);
            $firstId = substr($file[$group['Beforcount'] - 1], 0, strlen($group['id']));
            if ($firstId == $group['id']) {
                $result = true;
            }
        } else {
            if ($file) {
                $firstId = substr($file[1], 0, strlen($group['id']));
                if ((count($file) - 1) == $group['count'] && $firstId == $group['id']) {
                    $result = true;
                }
            }
        }
        return $result;
    }
}