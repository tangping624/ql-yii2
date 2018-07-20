<?php
namespace app\modules\shop\models;
use app\framework\web\extension\FormBase;
use app\entities\goods\SGoods;
class ShopForm extends FormBase {

    public $seller_id;

    public $type_id;

    public $type_pid;

    public $name;

    public $logo;

    public $content;

    public $summary;
    
    
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
