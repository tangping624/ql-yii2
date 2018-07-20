<?php

namespace app\modules\pub\controllers; 
use app\modules\ControllerBase;  

class ImageController extends ControllerBase {  
    private $_frameworkImageService; 
    public function __construct($id, $module,  \app\framework\services\ImageService $frameworkImageService, $config = []) {
        
        $this->_frameworkImageService = $frameworkImageService;
        parent::__construct($id, $module, $config);
    }

    //ajax 上传图片
    public function actionUpImage($sub_folder,$isthumbnail=false) {
        if (\Yii::$app->request->isGet) {
            $this->forbid();
        }

        $file = $_FILES['file'];
        $box = \Yii::$app->params['thumbnail_size'];
        if (empty($box) || !is_array($box) || count($box) != 2) {
            throw new InvalidConfigException('params 配置项thumbnail_size无效!');
        }
        $filename = $file["name"];
        $extor = pathinfo($filename, PATHINFO_EXTENSION);
        $filename= str_replace(".".$extor,".". strtolower($extor), $filename) ;
        $file["name"] = $filename;
        $result = $this->_frameworkImageService->upload($file, $sub_folder, $isthumbnail, $box);

        if ($result['status'] == 1) {
            return $this->json(["jsonrpc" => "2.0", "result" => $result['thumbnail'], "original_return" => $result['original'], "id" => "id"]);
        } else {
            return $this->json(["jsonrpc" => "2.0", "result" => $result['thumbnail'], "original_return" => $result['original'], "id" => "id"]);
        }
    }

}
