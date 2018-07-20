<?php
namespace app\modules\baike\models;
use app\framework\web\extension\FormBase;
use app\entities\baike\MWikiInfo;
use app\modules\advertise\models\ImageForm;
class WikiInfoForm extends FormBase {

    public $title;

    public $wiki_category_id;

    public $logo;

    public $content;


    public function convertToEntity(MWikiInfo $entity = NULL) {
        if (is_null($entity)) {
            $entity = new MWikiInfo();
        }

        $this->assignAttributes($entity);
        return $entity;
    }

  /*  //保存
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
    }*/


    protected function assignAttributes(MWikiInfo $entity) {
        $attrs = $this->getAttributes();
        $cols = MWikiInfo::getTableSchema()->columnNames;
        foreach ($attrs as $key => $value) {
            foreach ($cols as $col) {
                if (strtolower($key) == strtolower($col)) {
                    $entity->$col = $value;
                }
            }
        }
    }


}
