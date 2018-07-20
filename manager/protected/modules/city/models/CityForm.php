<?php

namespace app\modules\city\models;
use app\framework\web\extension\FormBase;
use app\entities\city\City;
class CityForm extends FormBase
{
    public $name;

    public $content;
    public $longitudes;
    public $latitudes;

    
    public function rules()
    {   //表单验证
        return [
            [['name','content'],'required','message'=>'不能为空'],
            [['longitudes','latitudes'],'safe']
        ];
    }

    public function convertToEntity(City $entity = NULL) {
        if (is_null($entity)) {
            $entity = new City();
        }
        $this->assignAttributes($entity);
        return $entity;
    }

    protected function assignAttributes(City $entity) {
        $attrs = $this->getAttributes();
        $cols =City::getTableSchema()->columnNames;
        foreach ($attrs as $key => $value) {
            foreach ($cols as $col) {
                if (strtolower($key) == strtolower($col)) {
                    $entity->$col = $value;
                }
            }
        }
    }
}
