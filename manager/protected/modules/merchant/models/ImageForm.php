<?php
/**
 * Created by PhpStorm.
 * User: tx-07
 * Date: 2016/8/9
 * Time: 9:46
 */
namespace app\modules\merchant\models;
use app\framework\web\extension\FormBase;
use app\entities\merchant\SImages;


class ImageForm extends FormBase{
    public $fid;
    /*
     原始url;
    */
    public $original_url;
    /*
     *
     缩略图url;
     * */
    public $thumb_url;

    public $created_by;

    public $modified_by;

    public function convertToEntity(SImages $entity = NULL)
    {
        if (is_null($entity)) {
            $entity = new SImages();
        }

        $this->assignAttributes($entity);
        return $entity;
    }

    protected function assignAttributes(SImages $entity){
        $attrs = $this->getAttributes();
        $cols = SImages::getTableSchema()->columnNames;
        foreach ($attrs as $key => $value) {
            foreach ($cols as $col) {
                if (strtolower($key) == strtolower($col)) {
                    $entity->$col = $value;
                }
            }
        }
    }
}