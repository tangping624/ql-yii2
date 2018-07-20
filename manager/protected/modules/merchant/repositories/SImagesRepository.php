<?php
namespace app\modules\merchant\repositories;
use app\entities\merchant\SellerType;
use app\entities\merchant\SImages;
use app\modules\RepositoryBase;
use app\framework\utils\DateTimeHelper;
class SImagesRepository extends RepositoryBase{

    public function deleteByFid($fid) {
        if (empty($fid)) {
            return null;
        }
        return SImages::deleteAll(['fid' => $fid]);
    }

    public function add($image) {
        if (count($image) < 1) {
            return 0;
        }
        try {
            // 批量插入问题描述
            foreach ($image as $img) {
                $img->created_on = DateTimeHelper::now();
                $img->modified_on = DateTimeHelper::now();
                $img->save();
            }
            return count($image);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function getImageInfo($id){
        if (empty($id)) {
            throw new \InvalidArgumentException('$id');
        }
        return  SImages::find()->where(['fid'=>$id])->select('*')->asArray()->all();
    }
}