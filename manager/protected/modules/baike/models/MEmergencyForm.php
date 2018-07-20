<?php
namespace app\modules\baike\models;
use app\framework\web\extension\FormBase;
use app\entities\baike\MEmergency;
use app\modules\advertise\models\ImageForm;
class MEmergencyForm extends FormBase {

    public $title;

    public $tel;

    public $logo;

    public $content;

    public $address;


    public function convertToEntity(MEmergency $entity = NULL) {
        if (is_null($entity)) {
            $entity = new MEmergency();
        }

        $this->assignAttributes($entity);
        return $entity;
    }



    protected function assignAttributes(MEmergency $entity) {
        $attrs = $this->getAttributes();
        $cols = MEmergency::getTableSchema()->columnNames;
        foreach ($attrs as $key => $value) {
            foreach ($cols as $col) {
                if (strtolower($key) == strtolower($col)) {
                    $entity->$col = $value;
                }
            }
        }
    }


}
