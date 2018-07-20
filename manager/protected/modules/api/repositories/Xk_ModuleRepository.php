<?php

namespace app\modules\api\repositories;

use yii\db\Query;
use app\repositories\RepositoryBase;

class Xk_ModuleRepository extends RepositoryBase {

    private $_dbConnection;
    public function __construct($dbConnetion)
    {
        $this->_dbConnection = $dbConnetion;
    }
    
    /**
     * 获取所有的模块列表
     * @param $tenantCode
     * @param $id
     * @return array|bool
     */
    public function getList() {
        $tenantDb = $this->_dbConnection;

        $query = new Query();
        $query->from('xk_module')->orderBy('id');
        $cmd = $query->createCommand($tenantDb);
        return $cmd->queryAll();
    }

    public function get($id) {
        $tenantDb = $this->_dbConnection;

        $query = new Query();
        $query->from('xk_module')->where([
            'id' => $id
        ]);
        $cmd = $query->createCommand($tenantDb);
        return $cmd->queryOne();
    }

}
