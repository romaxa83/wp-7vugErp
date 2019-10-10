<?php

namespace app\controllers\api;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * @SWG\Swagger(
 *     schemes={"http","https"},
 *     basePath="/",
 *     produces={"application/json"},
 *     consumes={"application/json"},
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Baza API",
 *         description="Api description..."
 *     )
 * )
 */
class IndexController extends Controller {

    public function actions(): array {
        return [
            'index' => [
                'class' => \yii2mod\swagger\SwaggerUIRenderer::class,
                'restUrl' => Url::to(['api/index/json-schema']),
            ],
            'json-schema' => [
                'class' => \yii2mod\swagger\OpenAPIRenderer::class,
                'cacheDuration' => 1,
                'scanDir' => [
                    Yii::getAlias('@app/controllers/api'),
                    Yii::getAlias('@app/modules')
                ],
            ],
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * @SWG\Get(path="/api/ping",
     *     tags={"Ping"},
     *     summary="Проверка сервера на работоспособность",
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
    public function actionPing() {
        return TRUE;
    }

}
