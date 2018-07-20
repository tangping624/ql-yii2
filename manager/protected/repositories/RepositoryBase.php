<?php

namespace app\repositories;

use yii\db\Connection;
use yii\base\InvalidCallException;
use yii\base\UnknownPropertyException;
use app\framework\db\EntityBase; 

/**
 * @property DbRoutingInterface $dbRouting dbRouting
 * @property Connection $tenantDb 租户db connection
 */
abstract class RepositoryBase
{

   
    /**
     * @return Connection
     */
    public function getTenantDb()
    {
        
        $conn = EntityBase::getDb();
        return $conn;
    }

    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            // read property, e.g. getName()
            return $this->$getter();
        }

        if (method_exists($this, 'set' . $name)) {
            throw new InvalidCallException('Getting write-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new UnknownPropertyException('Getting unknown property: ' . get_class($this) . '::' . $name);
        }
    }
}
