<?php 
namespace app\modules\appapi\controllers;   
use yii\web\Controller;
use yii\web\Response; 
use yii\filters\Cors;
use yii\helpers\ArrayHelper; 

abstract class AppControllerBase extends Controller
{           
    public $enableCsrfValidation = false; 
    public function __construct($id,
                                $module,
                                $config = [])
    { 
        parent::__construct($id, $module, $config);
    }
     public function behaviors()
    {
        return ArrayHelper::merge([
        [
            'class' => Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers'=>['*'],
                'Access-Control-Max-Age'=>86400 
            ] 
        ],
        ], parent::behaviors()); 
    }
    protected function json($data)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $data;
    }  
}