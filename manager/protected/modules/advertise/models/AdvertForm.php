<?php
namespace app\modules\advertise\models;
use app\framework\web\extension\FormBase;
use app\entities\advert\AAdvert;
use app\modules\advertise\models\ImageForm;
class AdvertForm extends FormBase {
    //标题
    public $title;
    //广告ID
    public $adsenseid;
    
    public $advert_images;

    public $advert_exts;


//保存
    public function getImageEntities($fid,$user_id,$isValid = true)
    {
        $image = [];
        if (empty($this->advert_images)) {
            return $image;
        }
        $arrImage = json_decode($this->advert_images, true);
        foreach ($arrImage as $image_temp) {
            $imageForm = new ImageForm();
            $imageForm->setAttributes($image_temp, false);
            if ($isValid && !$imageForm->validate()) {
                throw new \yii\base\InvalidValueException('ImageForm必填项校验未通过');
            }
            $imageForm->fid = $fid;
            $imageForm->created_by = $user_id;
            $imageForm->modified_by = $user_id;
            $image[] = $imageForm->convertToEntity();
        }
        return $image;
    }

        /**
     * 转换成对应实体
     * @return app\entities\s_gb_items
     */
    public function convertToEntity(AAdvert $entity = NULL) {
        if (is_null($entity)) {
            $entity = new AAdvert();
        }

        $this->assignAttributes($entity);
        return $entity;
    }
    protected function assignAttributes(AAdvert $entity) {
        $attrs = $this->getAttributes();
        $cols = AAdvert::getTableSchema()->columnNames;
        foreach ($attrs as $key => $value) {
            foreach ($cols as $col) {
                if (strtolower($key) == strtolower($col)) {
                    $entity->$col = $value;
                }
            }
        }
    }


}
