<?php

namespace app\services;

use app\repositories\PubAccountRepository;

class UploadService
{

    protected $pubAccountRepository;
    public function __construct(PubAccountRepository  $pubAccountRepository)
    {
        $this->pubAccountRepository = $pubAccountRepository;
    }

    public function uploadWxImage($publicId, $imgData)
    {
        $accessTokenRepository = \Yii::$container->get('app\framework\weixin\interfaces\IAccessTokenRepository');
        $accountRow = $this->pubAccountRepository->findAccountByPublicId($publicId);

        if ($accountRow == false) {
            throw new \Exception('publicId not exits, ' . $publicId);
        }

        $originalId = $accountRow['original_id'];

        $accessTokenHelper = new \app\framework\weixin\AccessTokenHelper($originalId, $accessTokenRepository);
        $media = new \app\framework\weixin\proxy\fw\Media($accessTokenHelper);

        //处理上传的图片
        $photos = [];
        $imgData = json_decode($imgData);

        if (!empty($imgData) && is_array($imgData) && count($imgData) > 0) {
            $imgs = $imgData;
            $i = 0;
            foreach ($imgs as $img) {
                $imageContent = $this->getImageContent($media, $img);
                $uploadResult = $this->uploadWxFiles("image.jpg", $imageContent);
                if (is_null($uploadResult)) {
                    $result['Result'] = false;
                    $result['msg'] = '提交失败,图片上传失败。';
                    return $result;
                }
                $photos[$i] = ["url" => $uploadResult['original'], "preview" => $uploadResult['preview']];
                $i++;
            }
        }

        return $photos;
    }

    /**
     * @param \app\framework\weixin\proxy\fw\Media $media
     * @param $mediaId
     * @return mixed
     */
    public function getImageContent($media, $mediaId)
    {
        $wxImageInfo = $media->get($mediaId);
        return $wxImageInfo;
    }

    public function uploadWxFiles($fileName, $fileData)
    {

        $downloadPath = $_SERVER ['DOCUMENT_ROOT'] . '/temp/img/';

        $extendName = strrchr($fileName, ".");
        $guid = uniqid();
        $actualName = $guid . $extendName;

        $filePath = $downloadPath . $actualName;

        $execResult = file_put_contents($filePath, $fileData);
        if ($execResult === false) {
            return null;
        }

        $file = [];
        $file["name"] = $actualName;
        $file['tmp_name'] = $filePath;

        $imgServ = new \app\framework\services\ImageService();
        $thumbnail_size = \Yii::$app->params['thumbnail_size'];
        $result = $imgServ->upload($file, 'upload', false, $thumbnail_size);
        $data = ["jsonrpc" => "2.0", "original" => $result['original'], "imgname" => $file['name'],
            "id" =>"id"];
        return $data;

    }

}