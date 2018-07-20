<?php
namespace app\modules\lobby\models;
use app\framework\web\extension\FormBase;
use app\entities\lobby\MBlog;
class MBlogForm extends FormBase {

    public $title;

    public $photo;

    public $source;

    public $content;

    public $member_id;


    public function convertToEntity(MBlog $entity = NULL) {
        if (is_null($entity)) {
            $entity = new MBlog();
        }

        $this->assignAttributes($entity);
        return $entity;
    }

    protected function assignAttributes(MBlog $entity) {
        $attrs = $this->getAttributes();
        $cols = MBlog::getTableSchema()->columnNames;
        foreach ($attrs as $key => $value) {
            foreach ($cols as $col) {
                if (strtolower($key) == strtolower($col)) {
                    $entity->$col = $value;
                }
            }
        }
    }


}
