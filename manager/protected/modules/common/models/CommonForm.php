<?php
namespace app\modules\common\models;
use app\framework\web\extension\FormBase;
use app\entities\goods\SGoods;
class CommonForm extends FormBase {

    public $seller_id;

    public $name;

    public $summary;

    public $content;

    public $logo;
    
    public $app_code;
    
    
    public function convertToEntity(SGoods $entity = NULL) {
        if (is_null($entity)) {
            $entity = new SGoods();
        }

        $this->assignAttributes($entity);
        return $entity;
    }

    protected function assignAttributes(SGoods $entity) {
        $attrs = $this->getAttributes();
        $cols = SGoods::getTableSchema()->columnNames;
        foreach ($attrs as $key => $value) {
            foreach ($cols as $col) {
                if (strtolower($key) == strtolower($col)) {
                    $entity->$col = $value;
                }
            }
        }
    }


}
