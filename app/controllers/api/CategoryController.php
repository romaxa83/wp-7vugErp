<?php

namespace app\controllers\api;

use app\models\Characteristic;
use app\models\CharacteristicValue;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Yii;
use yii\rest\Controller;
use app\models\Category;

class CategoryController extends Controller 
{
    /**
     * @SWG\Get(path="/api/getCategory",
     *     tags={"Category"},
     *     summary="Получение всех категорий с учетом вложенности",
     *     @SWG\Response(
     *         response = 200,
     *         description = "Ok",
     *         @SWG\Schema(ref = "#/")
     *     ),
     *     @SWG\Response(
     *         response = 400,
     *         description = "Bad Request",
     *         @SWG\Schema(ref = "#/")
     *     ),
     *     @SWG\Response(
     *         response = 404,
     *         description = "Not Found",
     *         @SWG\Schema(ref = "#/")
     *     ),
     *     @SWG\Response(
     *         response = 500,
     *         description = "Internal Server Error"
     *     )
     * )
    */
    public function actionGetCategory() 
    {
        $category = Category::find()->where(['not', ['publish_status' => NULL]])->orderBy(['position' => 'DESC'])->all();
        $cat = [];
        foreach ($category as $key => $value) {
            $cat[$key]['id'] = $value->id;
            $cat[$key]['name'] = $value->name;
            $cat[$key]['parent_id'] = $value->parent_id;
            $cat[$key]['status'] = $value->status;
            $cat[$key]['publish_status'] = $value->publish_status;
            $char_value = [];
            if($value->getSelectedChars()){
                $val_name = Characteristic::find()->select(['name'])->where(['in','id',$value->getSelectedChars()])->asArray()->all();
                $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($val_name));
                $arrOut = iterator_to_array($iterator, false);
                foreach ($value->getSelectedChars() as $id => $char){
                    foreach (CharacteristicValue::find()->select('name')->where(['id_char' => $char])->asArray()->all() as $i => $one){
                        $char_value[$char][] = $one['name'];
                    }
                }
                $cat[$key]['char_values'] = array_combine($arrOut,$char_value);
            }
        }
        return $cat;
    }
    /**
     * @SWG\Get(path="/api/getCategoryById",
     *     tags={"Category"},
     *     summary="Получение категории по уникальному идентификатору",
     *     @SWG\Parameter(
     *         name="id",
     *         in="query",
     *         description="Укажите ID",
     *         required=true,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "Ok",
     *         @SWG\Schema(ref = "#/")
     *     ),
     *     @SWG\Response(
     *         response = 400,
     *         description = "Bad Request",
     *         @SWG\Schema(ref = "#/")
     *     ),
     *     @SWG\Response(
     *         response = 404,
     *         description = "Not Found",
     *         @SWG\Schema(ref = "#/")
     *     ),
     *     @SWG\Response(
     *         response = 500,
     *         description = "Internal Server Error"
     *     )
     * )
     */
    public function actionGetCategoryById() 
    {
        $parent_name = 'Нет';
        $data = Yii::$app->request->get();
        $category = Category::findOne($data['id']);


        if (!isset($category)) {
            Yii::$app->response->statusCode = 400;
            return [
                'status' => 400,
                'message' => 'Категоря не найдена',
            ];
        }

        $char_value = [];
        if($category->getArrCharacteristic()){
            foreach ($category->getArrCharacteristic() as $id => $char){
                foreach (CharacteristicValue::find()->select('name')->where(['id_char' => $id])->asArray()->all() as $i => $one){
                    $char_value[$char][] = $one['name'];
                }
            }
        }

        if ($category->parent_id != 0) {
            $parent_name = Category::findOne($category->parent_id)->name;
        }
        return [
            'id' => $category->id,
            'name' => $category->name,
            'parent_id' => $category->parent_id,
            'parent_name' => $parent_name,
            'status' => $category->status,
            'char_values' => $char_value
        ];
    }

    private function addItem($mas, $parent_id) 
    {
        $data = [];
        foreach ($mas as $k => $v) {
            if ($v['parent_id'] == $parent_id) {
                $data[$k]['parent'] = $v;
                $data[$k]['child'] = $this->addItem($mas, $v['id']);
            }
        }
    }
}
