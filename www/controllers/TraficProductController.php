<?php
namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use app\models\Agent;
use app\models\Archive;
use app\models\TraficSearch;
use app\models\Operations;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use app\controllers\AccessController;

class TraficProductController extends BaseController
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
                        'actions' => ['index'],
                        'roles' => ['admin']
                    ]
                ]
            ]
        ];
    }

    public $agent;
    public $balance;
    public function actionIndex()
    {
        $model = new TraficSearch();
        $post = Yii::$app->request->post();
        if(empty($post)){
            $params = Yii::$app->session->get('trafic_session_filter');
        }else{
            $params = $post;
        }
        if(isset($params['TraficSearch']['name']) && !empty($params['TraficSearch']['name'])){
            $this->setFilterSession('trafic', $params);
            $dataProvider = $model->search($params);
            $arrayDataProdvider = new ArrayDataProvider();
            if($dataProvider->getTotalCount() > 0){
                $models = $dataProvider->getModels();
                $data = $this->GetStore($models[0]->id,$model->getDateRange());
                ArrayHelper::multisort($data, 'transaction');
                $this->getBalance($data);
                $arrayDataProdvider = new ArrayDataProvider([
                    'allModels' => $data,
                ]);
            }
        }
        return $this->render('index',['model' => $model,'dataProvider' => $dataProvider ?? [],'productHistory' => $arrayDataProdvider ?? [],'balance' => $this->balance]);
    }
    
    private function GetStore($id,$date)
    {
        $operations = Operations::find()
            ->select(['oper_coming.*','oper_consumption.*','oper_adjustment.*','operations.*'])
            ->leftJoin('oper_coming', 'oper_coming.transaction_id = operations.id')
            ->leftJoin('oper_consumption', 'oper_consumption.transaction_id = operations.id')
            ->leftJoin('oper_adjustment', 'oper_adjustment.transaction_id = operations.id')
            ->where(['or',
                ['oper_coming.product_id' => $id],
                ['oper_consumption.product_id' => $id],
                ['oper_adjustment.product_id' => $id]
            ])
            ->andWhere($date)
            ->orderBy(['operations.id' => SORT_ASC])
            ->all();
        $data = $this->GetNeedProduct($operations, $id);
        return array_merge($data, $this->GetStoreArchive($id,$date));
    }
    
    private function GetStoreArchive($id,$date)
    {
        $archive = Archive::find()
            ->leftJoin('archive_value', 'archive_value.archive_id = archive.id')
            ->where(['archive_value.product_id' => $id])
            ->andWhere($date)
            ->orderBy(['id' => SORT_ASC])
            ->all();
        return $this->GetNeedProduct($archive, $id);
    }
    
    private function getBalance($row)
    {
        $this->balance[] = 0;
        foreach ($row as $key => $one){
            $before = isset($this->balance[$key -1]) ? $this->balance[$key -1] : 0;
            if($one['type'] == 'приход'){
                $this->balance[$key] = $before + $one['row']->amount;
            }elseif($one['type'] == 'расход') {
                $this->balance[$key] = $before - $one['row']->amount;
            }elseif($one['type'] == 'коректировка') {
                $this->balance[$key] = $one['row']->amount;
            }
        }
    }

    private function GetNeedProduct($arr,$id)
    {
        $data = [];
        empty($this->agent) ? $this->agent = ArrayHelper::map(Agent::getAllAgent([1,2,3],true,[1,0]), 'id', 'firm') : null;
        foreach ($arr as $key => $one){
            $transaction_id = isset($one->transaction_id) ? $one->transaction_id : $one->id;
            $data[$transaction_id]['date'] = $arr[$key]->date;
            $data[$transaction_id]['transaction'] = $arr[$key]->transaction;
            $data[$transaction_id]['type'] = $arr[$key]->getTypeName();
            if($data[$transaction_id]['type'] == 1){
                $data[$transaction_id]['agent'] = $this->agent[$arr[$key]->where];
            }else{
                try{
                    $data[$transaction_id]['agent'] = $this->agent[$arr[$key]->whence];
                } catch (\Exception $e){
                    debug($arr[$key]);die();
                }
            }
            $one = $one->products;
            array_reduce($one,function($array,$item) use ($id){
                if($item->product_id == $id){
                    isset($array->amount) ? $item->amount += $array->amount : null;
                    return $item;
                }
            });
            $one = ArrayHelper::index($one,'product_id');
            $data[$transaction_id]['row'] = $one[$id];
        }
        return $data;
    }
}
