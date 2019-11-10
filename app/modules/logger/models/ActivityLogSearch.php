<?php

namespace app\modules\logger\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;
use yii\helpers\ArrayHelper;

/**
 * Class ActivityLogSearch
 * @package lav45\activityLogger\modules\models
 */
class ActivityLogSearch extends Model {

    /**
     * @var string
     */
    public $entityName;
    
    /**
     * @var string
     */
    public $action;
    
    /**
     * @var int|string
     */
    public $entityId;

    /**
     * @var int|string
     */
    public $userId;

    /**
     * @var string
     */
    public $env;

    /**
     * @var string
     */
    public $date;

    /**
     * @var string
     */
    public $date_from;

    /**
     * @var string
     */
    public $date_to;

    /**
     * @var array
     */
    private $entityMap;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['entityName'], 'in', 'range' => array_keys($this->getEntityMap())],
            [['entityId'], 'safe'],
            [['userId'], 'safe'],
            [['env'], 'safe'],
            [['action'], 'safe'],
            [['date'], 'date', 'format' => 'dd.MM.yyyy'],
            [['date_from'], 'date', 'format' => 'dd.MM.yyyy'],
            [['date_to'], 'date', 'format' => 'dd.MM.yyyy']
        ];
    }

    /**
     * For beautiful links in the browser bar when filtering and searching
     * @return string
     */
    public function formName() {
        return '';
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = ActivityLogViewModel::find()->orderBy(['id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);

        if (!($this->load($params, '') && $this->validate())) {
            return $dataProvider;
        }
        
        if (!empty($this->date_from) && !empty($this->date_to)) {
            $date_from = Yii::$app->getFormatter()->asTimestamp($this->date_from . ' 00:00:00 ' . Yii::$app->timeZone);
            $date_to = Yii::$app->getFormatter()->asTimestamp($this->date_to . ' 23:59:59 ' . Yii::$app->timeZone);
            $query->andFilterWhere(['and',
                ['>=', 'created_at', $date_from],
                ['<=', 'created_at', $date_to],
            ]);
        }

        $query->andFilterWhere(['entity_name' => $this->entityName])
              ->andFilterWhere(['entity_id' => $this->entityId])
              ->andFilterWhere(['user_id' => $this->userId])
              ->andFilterWhere(['action' => $this->action]);

        return $dataProvider;
    }

    /**
     * @return array
     */
    protected function getEntityMap() {
        return $this->entityMap;
    }

    /**
     * @param array $value
     */
    public function setEntityMap($value) {
        $this->entityMap = $value;
    }

    /**
     * @return array
     */
    public function getEntityNameList() {
        $data = array_keys($this->getEntityMap());
        return array_combine($data, $data);
    }
    
    public function getUserList() {
        $users = User::find()->asArray()->select(['id', 'username'])->groupBy(['username'])->all();
        return ArrayHelper::map($users, 'id', 'username');
    }
    
    public function getActionList() {
        return [
            'created' => 'создал',
            'updated' => 'обновил',
            'removed' => 'удалил'
        ];
    }
    /*
    *Метод возрашает записуемые поля в метод csv  
    */
    public function exportFields()
    {
        return [
            'id',
            'user_name',
            'entity_name',
            'action',
            'created_at',
            'data',
        ];
    }
    /*
    *метод записует данные в csv файл 
    */
    public function Csv($group,$offset,$countPage,$key)
    {
        $date = gmdate("Y-m-d",$group[0]['created_at']);
        $year = substr($date,0,-6);
        $month = substr($date,5,-3);
        if(!file_exists("uploads/log-archive/".$year)){            
            mkdir('uploads/log-archive/'.$year,0777);
        }
        if(!file_exists("uploads/log-archive/".$year.'/'.$month)){            
            mkdir('uploads/log-archive/'.$year.'/'.$month,0777);
        }
        $filename = "uploads/log-archive/".$year.'/'.$month.'/'.substr($date,8).'.csv';
        if(file_exists($filename) && $offset == 0){
            $file = count(file($filename))-1;
            $_SESSION['group'][$key]['Beforcount'] = $_SESSION['group'][$key]['count'];
            $_SESSION['group'][$key]['count'] += $file;
            $fp = fopen($filename, 'a');
        }else{
            $fp = fopen($filename, 'a');
            if($offset == 0){
                ob_start();
                $fields = $this->exportFields();
                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Content-Description: File Transfer');
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment;filename='.$filename);
                header('Content-Transfer-Encoding: binary');

                fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

                $header = [];
                $i = 0;
                foreach ($fields as $one) {
                    $header[$i] = $one;
                    $i++;
                }
                fputs($fp, implode($header, ';')."\n");
            }
        }
        if($offset != $countPage){
            $items = [];
            $i = 0;
            foreach ($group as $model) {
                foreach ($this->exportFields() as $key) {
                    if($key == 'created_at'){
                        $items[$i] = gmdate("Y-m-d",$model[$key]);
                    }else{
                        $items[$i] = $model[$key];
                    }
                    $i++;
                }
                $string = implode($items, ';');
                fputs($fp, $string."\n");
                $items = [];
                $i = 0;
            }
        }
        fclose($fp);
    }
}