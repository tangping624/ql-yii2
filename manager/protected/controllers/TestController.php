<?php 
namespace app\controllers;  
use yii\web\Controller;
use Yii;
use yii\web\Response;
class TestController  extends Controller{
    public $enableCsrfValidation = false;
    public function __construct($id, $module,  $config = [])
    { 
        parent::__construct($id, $module, $config);
    } 
     public function actionIndex(){ 
         $path = static::getAbsoluteExcelTemplatePath('/protected/config/env/dev.php');
         $orgin_str = file_get_contents($path);
         $update_str = str_replace('db', 'db000', $orgin_str);
        file_put_contents($path, $update_str);
       // return $this->json( Yii::$app->get('db000'));
     }
       protected function json($data)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $data;
    }
     public function actionGet(){
                   $path = static::getAbsoluteExcelTemplatePath('/protected/config/env/dev.php');
         $orgin_str = file_get_contents($path);
         $update_str = str_replace('db000', 'db', $orgin_str);
        file_put_contents($path, $update_str);
         return $this->json( Yii::$app->get('db'));
     } 
       public function actionSet(){ 
         $path = static::getAbsoluteExcelTemplatePath('/protected/config/inc/params.php');
         $orgin_str = file_get_contents($path);
         $update_str = str_replace('00000001', '00000000', $orgin_str);
        file_put_contents($path, $update_str);
       // return $this->json( Yii::$app->get('db000'));
     }
     
        public function actionUpdate(){ 
         $path = static::getAbsoluteExcelTemplatePath('/protected/config/inc/params.php');
         $orgin_str = file_get_contents($path);
         $update_str = str_replace('00000000', '00000001', $orgin_str);
        file_put_contents($path, $update_str);
       // return $this->json( Yii::$app->get('db000'));
     }
    public static function getAbsoluteExcelTemplatePath($relativePath)
    {
        $path = dirname($_SERVER['DOCUMENT_ROOT']) .  $relativePath  ;
        if (file_exists($path)) {
            return $path;
        }
        return '';
    }
}
