<?php

namespace app\repositories;

class PubAccountRepository extends RepositoryBase
{
    public function findAccountByPublicId($accountId)
    {
        $sql = "select original_id from p_account where id=:id and is_deleted=0";
        $result = $this->tenantDb->createCommand($sql, [':id' => $accountId])->queryOne();
        return $result;
    }
}
