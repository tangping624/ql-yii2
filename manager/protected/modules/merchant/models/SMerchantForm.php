<?php
/**
 * Created by PhpStorm.
 * User: tx-06
 * Date: 2016/8/4
 * Time: 9:15
 */
namespace app\modules\merchant\models;
use app\framework\web\extension\FormBase;
use app\entities\merchant\SMerchant;
use app\modules\merchant\models\ImageForm;

class SMerchantForm extends FormBase{

    public $is_recommend;
    public $name;
    public $linkman;
    public $linktel;
    public $address;
    public $longitudes;
    public $latitudes;
    public $city;
    public $content;
    public $logo;
    public $type_pid;
    public $type_id;
    public $city_id;
    public $city_pid;
    public $remind;
    public $summary;
    public $goods_images;
    public $is_deleted;
    public $sort;
    public $mail;
    public $fax;

    
    public function convertToEntity(SMerchant $entity = NULL) {
        if (is_null($entity)) {
            $entity = new SMerchant();
        }
        $this->assignAttributes($entity);
        return $entity;
    }
  /* public function rules(){   //表单验证
        return [
            [['goodsname','price','goodsinfo','type_id'],'required','message'=>'不能为空'],
        ];
    }*/

    public function getImageEntities($fid, $created_by = null, $modified_by = null, $isValid = true) {
        $image = [];
        if (empty($this->goods_images)) {
            return $image;
        }
        $arrImage = json_decode($this->goods_images, true);
        foreach ($arrImage as $image_temp) {
            $imageForm = new ImageForm();
            $imageForm->setAttributes($image_temp, false);
            if ($isValid && !$imageForm->validate()) {
                throw new \yii\base\InvalidValueException('ImageForm必填项校验未通过');
            }
            $imageForm->fid = $fid;
            $imageForm->created_by = $created_by;
            $imageForm->modified_by = $modified_by;
            $image[] = $imageForm->convertToEntity();
        }
        return $image;
    }



    protected function assignAttributes(SMerchant $entity) {
        $attrs = $this->getAttributes();
        $cols =SMerchant::getTableSchema()->columnNames;
        foreach ($attrs as $key => $value) {
            foreach ($cols as $col) {
                if (strtolower($key) == strtolower($col)) {
                    $entity->$col = $value;
                }
            }
        }
    }
}
