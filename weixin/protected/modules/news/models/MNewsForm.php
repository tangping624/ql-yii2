<?php
namespace app\modules\news\models;
use app\framework\web\extension\FormBase;
use app\entities\lobby\MNews;
class MNewsForm extends FormBase {

    public $title;

    public $photo;

    public $source;

    public $content;

    public $member_id;

    public $type_id;


    public function convertToEntity(MNews $entity = NULL) {
        if (is_null($entity)) {
            $entity = new MNews();
        }

        $this->assignAttributes($entity);
        return $entity;
    }

    protected function assignAttributes(MNews $entity) {
        $attrs = $this->getAttributes();
        $cols = MNews::getTableSchema()->columnNames;
        foreach ($attrs as $key => $value) {
            foreach ($cols as $col) {
                if (strtolower($key) == strtolower($col)) {
                    $entity->$col = $value;
                }
            }
        }
    }


}
