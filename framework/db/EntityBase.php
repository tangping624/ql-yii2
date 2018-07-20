<?php

namespace app\framework\db;

use yii\db\ActiveRecord;
use Yii; 
use app\framework\utils\StringHelper;

abstract class EntityBase extends ActiveRecord
{
    protected $is_uniqid = true;
    protected static $orgDbConnCollection = []; 
    protected function getAutoInsertingColumns(){
        return [];
    }

    protected  function getAutoUpdatingColumns(){
        return [];
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            if (empty($this->id) && $this->is_uniqid) {
                $this->id = StringHelper::uuid();
            }

            // 自动生成创建时间、修改时间等
            $schema = static::getTableSchema();
            $attrs = $this->getAutoInsertingColumns();
            $this->fillColumn($schema, $attrs);
        } else {
            // 自动生成创建时间、修改时间等
            $schema = static::getTableSchema();
            $attrs = $this->getAutoUpdatingColumns();
            $this->fillColumn($schema, $attrs);
        }

        return true;
    }

    private function fillColumn($schema, $attrs)
    {
        if (empty($attrs)) {
            return;
        } 
        foreach ($attrs as $name => $value) {
            $col = $schema->getColumn($name);
            if (isset($col)) {
                $this->$name = $value;
            }

        }
    }

    /**
     * @param array|object $db_dsn
     * @return \yii\db\Connection
     */
    public static function toConnection()
    {
          return new \yii\db\Connection(Yii::$app->get('db'));   
    }
}
