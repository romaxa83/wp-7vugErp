<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Product;
use yii\helpers\ArrayHelper;
use PHPExcel_Reader_Excel5;
/**
 * ProductSearch represents the model behind the search form about `app\models\Product`.
 */
class ProductSearch extends Product
{
    public $agent;
    public $category;
    public $warning;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','vendor_code','id_char'], 'integer'],
            [['name','agent_id','category_id'],'string'],
            [['name','agent','category', 'created_at', 'warning'], 'safe'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    public function exportFields()
    {
        return [
            'id',
            'vendor_code',
            'name',
            'category_id',
            'agent_id',
            'amount',
            'unit',
            'start_price',
            'cost_price',
            'trade_price',
            'price1',
            'price2',
            'created_at'
        ];
    }
    /**
     * Метод фильтраций записей на вывод 
     * @param array $params
     * @return ActiveDataProvider
    */
    public function search($params)
    {
        $query = Product::find();
        //pagination and sort
        if (isset($params['pdf'])) {
            $pagination = FALSE;
            $sort = FALSE;
        }else{
            $sort = [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ];
            $pagination = [
                'pageSize' => (getSizePage('prod') > 0) ? getSizePage('prod') : 10,
                'page' => (isset($params['page'])) ? $params['page'] - 1 : 0
            ];
        }
        //create dataProvider
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => $sort,
            'pagination' => $pagination,
        ]);
        //check validate params
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        //join 
        $query->joinWith(['agent','category']);
        //start filter data
        if(!empty($this->warning) && $this->warning == 1){
            //filte by warning or error for amount 
            $query->orWhere('product.min_amount > product.amount')
                ->orWhere('product.amount < (product.min_amount + (20 * product.min_amount) / 100)');
            //filte by warning or error for price1 or price2 
            $secondPartConditional['error'] = '<= (product.cost_price * '. getUsd() .')';
            $secondPartConditional['warning'] = '< ('. getUsd() .' * (product.cost_price + (product.margin * product.cost_price) / 100))';
            $query->orWhere('product.price1' . $secondPartConditional['error'])
                ->orWhere('product.price1' . $secondPartConditional['warning']);
            $query->orWhere('product.price2' . $secondPartConditional['error'])
                ->orWhere('product.price2' . $secondPartConditional['warning']);
        }
        if(!empty($this->name)){
            $query->andWhere(['or',
                ['like', 'product.name', $this->name],
                ['like', 'vendor_code', $this->name]
            ]);
        }
        if(!empty($this->created_at)){
            $query->andFilterWhere(['and',
                ['>=', 'product.created_at', strtotime($this->created_at . ' 00:00:00')],
                ['<=', 'product.created_at', strtotime($this->created_at . ' 23:59:59')]
            ]);
        }
        if(!empty($this->category)){
            $category = Category::GetIdChild(Category::addItem(Category::find()->where(['status' => 1])->asArray()->all(), $this->category), $this->category);
            $arr = explode(',',$category);
            $arr[count($arr) - 1] = $this->category;
            $query->andFilterWhere(['in','product.category_id',$arr]);
            $query->orderBy('product.category_id');
        }
        if($this->agent != 'Поставщиики'){
            $agent_id = Agent::find()->select(['id'])->where(['firm' => $this->agent])->asArray()->one();
            $additional_id = ProductAgent::find()->select(['product_id'])->where(['agent_id' => $agent_id['id']])->asArray()->all();
            $query->andFilterWhere(['or',
                ['product.agent_id' => $agent_id['id']],
                ['in','product.id',ArrayHelper::getColumn($additional_id, 'product_id')]
            ]);
        }
        return $dataProvider;
    }
    /**
     * Экспорт продуктов в csv файл
     * @param integer $offset используеться для вытяжки след товаров
     * @return boolean true - если записаны все продукты, иначе - false
    */
    public function Csv($offset)
    {
        $models = $this->getData($offset);
        if(empty($models)) {
            return true;
        }
        $filename = "uploads/product.csv";
        $fp = fopen($filename, 'a');
        if($offset == 0){
            ob_start();
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Description: File Transfer');
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename='.$filename);
            header('Content-Transfer-Encoding: binary');
            fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

            fputs($fp, implode($this->getHeaderArray(), ';')."\n");
        }
        foreach ($models as $model) {
            $string = implode($this->getItemArray($model), ';');
            $string = str_replace('.',',',$string);
            fputs($fp, $string."\n");
        }
        fclose($fp);
        return false;
    }
    /**
     * Экспорт продуктов в Excel файл
     * @param integer $offset используеться для вытяжки след товаров
     * @return boolean true - если записаны все продукты, иначе - false
    */
    public function Excel($offset)
    {
        $models = $this->getData($offset);
        if(empty($models)) {
            return true;
        }
        $filename = "product.xls";
        $path = 'uploads/'.$filename;
        $limit = getSizePage('prod');
        $row = $limit*$offset + 2;
        $items[] = [];
        $i = 0;
        if($offset == 0) {
            $objPHPExcel = new \PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $items[$i] = $this->getHeaderArray();
            $i++;
            $row = 1;
        } else {
            $objReader = new PHPExcel_Reader_Excel5();
            $objPHPExcel = $objReader->load($path);
            $objPHPExcel->setActiveSheetIndex(0);
        }
        foreach ($models as $model) {
            $items[$i] = $this->getItemArray($model);
            $i++;
        }
        $objPHPExcel->getActiveSheet()->fromArray($items, NULL, 'A'. $row);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$filename);
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($path);
        $objPHPExcel->disconnectWorksheets();
        unset($objPHPExcel);
        return false;
    }
    /**
     * Возвращает массив продуктов для текущей итерации записи
     * @param integer $offset используеться для вытяжки след товаров
     * @return array массив товаров 
    */
    private function getData($offset)
    {
        $limit = getSizePage('prod');
        $models = Product::find()->with(['category'])->with(['agent'])->asArray()->limit($limit)->offset($offset*$limit)->all();
        return $models;
    }
    /**
     * @return array массив с заголовками для записи таблицы в файл
    */
    private function getHeaderArray()
    {
        $header = [];
        $fields = $this->exportFields();

        foreach ($fields as $one) {
            $header[] = $this->getAttributeLabel($one);
        }

        return $header;
    }
    /**
     * Метод форматирования данных для записи в файл 
     * @param array $model модель Product 
     * @return array массив полей для записи в файл  
    */
    private function getItemArray($model)
    {
        $item = [];
        foreach ($this->exportFields() as $key) {
            switch ($key) {
                case 'category_id':
                    $item[] = $model['category']['name'];
                    break;
                case 'agent_id':
                    $item[] = $model['agent']['firm'];
                    break;
                case 'created_at':
                    $item[] = gmdate("Y-m-d H:i:s", $model[$key]);
                    break;
                default:
                    $item[] = $model[$key];
                    break;
            }
        }
        return $item;
    }

}
