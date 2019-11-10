<?php

namespace app\controllers;

use app\models\User;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use app\models\Agent;
use app\models\Settings;

class BaseController extends Controller
{
    /**
     * Переопределения init() с запись настроек в сессию так же если роль текущего user агент запись его id в сессию
    */
    public function init()
    {
        date_default_timezone_set("Europe/Kiev");

        $settings = Settings::find()->asArray()->one();
        Yii::$app->session->set('getSettingSession', $settings);

        if (isset(Yii::$app->user->identity->store_id)){
            $store_price = Agent::find()->where(['id' => Yii::$app->user->identity->store_id])->asArray()->one();
            Yii::$app->session->set('getStorePrice', $store_price['price_type']);
            Yii::$app->session->set('getStoreId', Yii::$app->user->identity->store_id);
        }
    }
    /**
     * Запись фильтра в сессию
    */
    protected function setFilterSession($type, $queryParams)
    {
        if (count($queryParams) > 0) {
            if (isset($queryParams['page'])) {
                $filter = Yii::$app->session->get($type . '_session_filter');
                $filter['page'] = $queryParams['page'];
                Yii::$app->session->set($type . '_session_filter', $filter);
            } else {
                Yii::$app->session->set($type . '_session_filter', $queryParams);
            }
        }
    }
    /**
     * Удаления с сессий сообщения которые уже выведены   
    */
    public function actionUnsetWarning()
    {
        unset($_SESSION['warning']);
    }

    public function actionSwitchEditMode(){
        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post('status');
            $status = $post == 'true' ? true : false;
            Yii::$app->session->set('EditMode',$status);
        }
    }

    /**
     * провереряет пользователя auth_key и если он
     * изменился (при изменении пароля) разлогиниваем пользователя
     */
    public function checkAuthKey()
    {
        if(Yii::$app->user->identity->old_auth_key){
            if(!Yii::$app->user->identity->validateAuthKey(Yii::$app->user->identity->old_auth_key)){
                $user = User::findOne(Yii::$app->user->identity->id);
                $user->old_auth_key = $user->auth_key;
                $user->save();

                return $this->redirect(Url::to('/site/logout'));
            }
        }
    }
}
