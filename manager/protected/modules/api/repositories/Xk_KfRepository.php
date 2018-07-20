<?php

namespace app\modules\api\repositories;

use yii\db\Query;
use app\repositories\RepositoryBase;

class Xk_KfRepository extends RepositoryBase
{
    private $_dbConnection;
    public function __construct($dbConnetion)
    {
        $this->_dbConnection = $dbConnetion;
    }
    
    /**
     * 获取客服数据
     */
    public function getList($moduleId)
    {
        $tenantDb = $this->_dbConnection;

        $query = new Query();
        $query->from('xk_kf')->where([
            'module_id' => $moduleId
        ])->orderBy('id');
        $cmd = $query->createCommand($tenantDb);
        return $cmd->queryAll();
    }
    
    /**
     * 获取客服数据
     */
    public function get($id)
    {
        $tenantDb = $this->_dbConnection;

        $query = new Query();
        $query->from('xk_kf')->where([
            'id' => $id
        ]);
        $cmd = $query->createCommand($tenantDb);
        return $cmd->queryOne();
    }
}