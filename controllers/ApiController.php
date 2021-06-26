<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;

class ApiController extends Controller
{

    public function actionCheck()
    {
        // можно так
        // $response = Yii::$app->response;
        // $response->format = \yii\web\Response::FORMAT_JSON;
        // $response->data = ['status' => 'alive'];

        // или так
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['status' => 'alive'];
    }

}