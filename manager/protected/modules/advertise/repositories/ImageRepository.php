<?php
namespace app\modules\advertise\repositories;
use app\modules\RepositoryBase;
use app\entities\advert\AImages;
use app\framework\utils\DateTimeHelper;
use Exception;
class ImageRepository extends RepositoryBase {
    
    public function getImageByFid($fid) {
        if (empty($fid)) {
            return null;
        }

        return AImages::find()->where(['fid' => $fid])->orderBy('id')->all();
    }

    public function deleteByFid($fid) {
        if (empty($fid)) {
            return null;
        }
        return AImages::deleteAll(['fid' => $fid]);
    }

    /**
     *
     * @param array[r_image] $images
     * @return integer number of rows affected by the execution.
     * @throws Exception execution failed
     */
    public function add($images) {
        if (count($images) < 1) {
            return 0;
        }
        try {
            // 批量插入问题描述   
            foreach ($images as $image) {
                $image->created_on = DateTimeHelper::now();
                $image->modified_on = DateTimeHelper::now();
                $image->save();
            }
            return count($images);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    

}