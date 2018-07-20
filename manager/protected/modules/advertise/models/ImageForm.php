<?php
namespace app\modules\advertise\models;
use app\framework\web\extension\FormBase;
use app\entities\advert\AImages;

class ImageForm extends FormBase {
     /**
     * @var string
     */
    public $thumb_url;

    /**
     * @var string 
     */
    public $original_url;
    
    /**
     * @var string 
     */
    public $fid;
    
    /**
     * @var string 
     */
    public $created_by;
    
    /**
     * @var string 
     */
    public $modified_by;

    public $link_url;

    /**
     * 转换成对应实体
     * @return \app\entities\r_image
     */
    public function convertToEntity() {
        $entity = new AImages();
        $this->assignAttributes($entity);
        return $entity;
    }

    protected function assignAttributes(AImages $entity) {
        $attrs = $this->getAttributes();
        $cols = AImages::getTableSchema()->columnNames;
        foreach ($attrs as $key => $value) {
            foreach ($cols as $col) {
                if (strtolower($key) == strtolower($col)) {
                    $entity->$col = $value;
                }
            }
        }
    }
}
