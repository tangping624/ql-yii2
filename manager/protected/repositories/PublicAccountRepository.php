<?php

namespace app\repositories;

use yii\db\Connection;
use yii\db\Query;
use app\framework\db\EntityBase;

class PublicAccountRepository extends RepositoryBase
{
    /**
     * 获取公众号信息
     * @param $tenantCode
     * @param $id
     * @return array|bool
     */
    public function getAccount( $id)
    {
        if(empty($id)){
            return false;
        }

        $tenantDb = EntityBase::getDb(); 
        $query = new Query();
        $query->from('p_account')->where(['id'=>$id, 'is_deleted' => 0])->select('id, name, original_id, wechat_number, type,app_id, app_secret, token, mch_id, mch_key,package_type');
        $cmd = $query->createCommand($tenantDb);
        return $cmd->queryOne();

    }

    public function getTenantDbByTenantCode($tenantCode)
    {
        $query = new Query();
        $cmd = $query->from('tenant')->innerJoin('rds', 'tenant.rds_id=rds.id')
            ->where('tenant.is_deleted=0 and enabled=1 and tenant.code=:tenantCode')
            ->select('tenant.db_name, rds.host, rds.account, rds.pwd, rds.port')
            ->createCommand();

        $cmd->bindValue(':tenantCode', $tenantCode);
        $row = $cmd->queryOne();
        
        if($row != false){
            $db = new \yii\db\Connection([
                'dsn' => 'mysql:host=' . $row['host'] . ';dbname=' . $row['db_name'],
                'username' => $row['account'],
                'password' => $row['pwd'],
                'charset' => 'utf8',
                'enableSchemaCache' => true,
            ]);
            return $db;
        }else{
            throw new \Exception('没有找到rds，租户代码为: ' .$tenantCode);
        }


    }


}