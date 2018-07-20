<?php

namespace app\modules\api\repositories;

use yii\db\Query;
use app\repositories\RepositoryBase;

class Xk_SessionRepository extends RepositoryBase {

    private $_dbConnection;
    public function __construct($dbConnetion)
    {
        $this->_dbConnection = $dbConnetion;
    }
    
    /**
     * 获取会话数据
     */
    public function get($userId) {
        $tenantDb = $this->_dbConnection;

        $query = new Query();
        $query->from('xk_session')->where([
            'user_id' => $userId
        ]);
        $cmd = $query->createCommand($tenantDb);
        return $cmd->queryOne();
    }

    /**
     * 插入会话数据
     */
    public function insert($session) {
        $tenantDb = $this->_dbConnection;

        $command = $tenantDb->createCommand()->insert('xk_session', $session);

        return $command->execute();
    }

    /**
     * 更新会话数据
     */
    public function update($session) {
        $tenantDb = $this->_dbConnection;

        $command = $tenantDb->createCommand();

        $condition = [
            'user_id' => $session['user_id']
        ];

        $command->update('xk_session', $session, $condition);
        return $command->execute();
    }

    /**
     * 删除会话
     */
    public function delete($userId) {
        $tenantDb = $this->_dbConnection;

        $command = $tenantDb->createCommand();

        $condition = [
            'user_id' => $userId
        ];

        $command->delete('xk_session', $condition);

        return $command->execute();
    }

}
