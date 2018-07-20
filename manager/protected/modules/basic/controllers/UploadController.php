<?php
/**
 * 图片上传
 * User: robert
 * Date: 2015/5/7
 * Time: 10:59
 */
namespace app\modules\basic\controllers;

use app\modules\ControllerBase;
use Yii;
use yii\base\Response;

class UploadController extends ControllerBase
{

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    /**
     * 上传图片
     * @return array ['status'=>1, 'original'=>url, 'thumbnail'=>url2 , 'msg'=>'' ] status:0 表示失败
     */
    public function actionUploadImage()
    {
        $file = $_FILES["file"];
        $imgServ = new \app\framework\services\ImageService();
        $thumbnail_size = \Yii::$app->params['thumbnail_size'];
        $result = $imgServ->upload($file, 'upload', false, $thumbnail_size);
        $data=["jsonrpc" => "2.0", "original" => $result['original'], "imgname" => $file['name'], "preview" => $result['thumbnail'], "id" => "id"];
        Yii::$app->response->format = 'html';
        return json_encode($data);
    }
}
