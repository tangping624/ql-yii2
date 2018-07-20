<?php

namespace app\modules\oauth\repositories;

use OAuth2\Storage\Pdo;
use yii\db\Connection;
use yii\web\HttpException; 
use app\framework\biz\cache\OrganizationCacheManager;
use app\framework\db\EntityBase;
class OAuth2Pdo extends pdo
{
    public $dsn;

    public $username;

    public $password;

    public $connection = 'db';

    public function __construct($connection = null, $config = [])
    {
        if ($connection === null) {
            if (!empty($this->connection)) {
                $connection =  EntityBase::getDb(); 
                if (!$connection->getIsActive()) {
                    $connection->open();
                }
                $connection = $connection->pdo;
            } else {
                $connection = [
                    'dsn' => $this->dsn,
                    'username' => $this->username,
                    'password' => $this->password
                ];
            }
        }

        parent::__construct($connection, $config);
    }
 
}
