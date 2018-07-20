<?php
/**
 * 公众号数据访问
 * User: lvq
 * Date: 2015/5/5
 * Time: 16:17
 */
namespace app\modules\wechat\repositories;

use app\entities\PAccount;
use app\entities\PAccountApp;
use app\framework\db\SqlHelper;
use app\framework\utils\StringHelper;
use app\models\Organization;
use app\modules\RepositoryBase;
use app\framework\db\ConfigEntity;
use app\entities\EntityBase;
use yii\db\Query;
use app\entities\TVAppFunction;

class AccountRepository extends RepositoryBase
{
    
    public function getEntry($accountId){
        $query = TVAppFunction::find()
                ->innerjoin('p_account',"p_account.id='".$accountId."'")
                ->innerjoin('t_account_app',"t_account_app.account_id='".$accountId."' and t_account_app.app_code = t_vappfunction.app_code")
                ->where('t_vappfunction.package_type=p_account.package_type and t_vappfunction.is_deleted=0') 
                ->select('t_vappfunction.id,t_vappfunction.app_code,t_vappfunction.img_url,t_vappfunction.name,t_vappfunction.relative_url')
                ->asArray()->all();
        return $query;
    }
    /**
     * 查找公司下的所有公众号
     * @param string $corpId
     * @return array
     */
    public function getAccounts($corpId)
    {
        $query = (new Query())
            ->select("a.id,a.name,a.original_id,a.wechat_number,a.type,a.app_secret,a.qrcode_url,a.headimg_url,a.is_authed,a.app_id")
            ->from('p_account a') 
            ->where('a.is_deleted=0');
                

        if (strtolower($corpId) != SUPER_ORGANIZATION_ID) {
            $query->andWhere(["=", "a.corp_id", $corpId]);
        }

        $rows = $query->createCommand(EntityBase::getDb())->queryAll();
        return $rows;
    }
    
    /**
     * 根据id查询公众号信息
     * @param string $id
     * @return array|false
     */
    public function findAccountInfoById($id)
    {
        $sql = "select a.id,a.name,a.original_id,a.wechat_number,a.type,a.app_secret,a.qrcode_url,a.headimg_url,a.is_authed,"
                . "a.app_id,if(ifnull(a.mch_ssl_cert,'')='',0,1) as is_import_ssl_cert,if(ifnull(a.mch_ssl_key,'')='',0,1) as is_import_ssl_key,mch_id,mch_key "
            . "from p_account a "
            . "where a.is_deleted=0 and a.id=:id";
        
        $dbConn = EntityBase::getDb();
        $row = $dbConn->createCommand($sql, [':id' => $id])->queryOne();
        return $row;
    }
    
    /**
     * 根据appId查找公众号信息
     * @param string $appId
     * @return array|false
     */
    public function findAccountInfoByAppId($appId)
    {
        $sql = "select a.id,a.name,a.original_id,a.wechat_number,a.type,a.app_secret,a.qrcode_url,a.headimg_url,a.is_authed,a.app_id,"
            . "if(ifnull(a.mch_ssl_cert,'')='',0,1) as is_import_ssl_cert,if(ifnull(a.mch_ssl_key,'')='',0,1) as is_import_ssl_key,mch_id,mch_key "
            . "from p_account a " 
            . "where a.is_deleted=0 and a.app_id=:appId";
        
        $dbConn = EntityBase::getDb();
        $row = $dbConn->createCommand($sql, [':appId' => $appId])->queryOne();
        return $row;
    }
    
    /**
     * 删除公众号
     * @param string $accountId
     */
    public function remove($accountId)
    {
        $row = $this->getAccountById($accountId);
        if ($row) {
            SqlHelper::update("wechat_account_mapping", ConfigEntity::getDb(), ['is_deleted' => 1], "account_app_id=:appId", [":appId" => $row['app_id']]);
        }
        
        return SqlHelper::update('p_account', EntityBase::getDb(), ['is_deleted' => 1], "id=:id", [':id' => $accountId?:'']);
    }
    
    /**
     * 根据appId查找租户映射信息
     * @param string $appId
     * @return array|false
     */
    public function getAccountMappingTenanantCode($appId)
    {
        $query = new \yii\db\Query();
        $row = $query->from("wechat_account_mapping")
               ->select("id, account_app_id, tenant_code")
               ->where("is_deleted=0 and account_app_id=:appId", [":appId" => $appId])
               ->createCommand()
               ->queryOne();

        return $row;
    }
    
    /**
     * 新增公众号
     * @param string $tenantCode 租户代码
     * @param array $accountDataRow p_account字段键值对数组
     * @return type
     */
    public function insertWechatAccount($tenantCode, $accountDataRow)
    {
        // 映射表－配置库
        $configDataRow = ["account_app_id" => $accountDataRow["app_id"], "tenant_code" => $tenantCode, "is_deleted" => 0];
        SqlHelper::insert("wechat_account_mapping", ConfigEntity::getDb(), $configDataRow);
        // 公众号表－租户库
        $accountDataRow['id'] = $accountDataRow['id']?:StringHelper::uuid();
        SqlHelper::insert('p_account', EntityBase::getDb(), $accountDataRow);
        return $accountDataRow['id'];
    }
    
    /**
     * 重新授权公众号
     * @param string $tenantCode
     * @param string $oldAppId
     * @param array $accountDataRow
     * @return type
     */
    public function updateWechatAccount($tenantCode, $oldAppId, $accountDataRow)
    {
         // 映射表－配置库
        $configDataRow = ["account_app_id" => $accountDataRow["app_id"], "tenant_code" => $tenantCode, "is_deleted" => 0];
        $mappingRow = $this->getAccountMappingTenanantCode($oldAppId);
        if ($mappingRow) {
            SqlHelper::update("wechat_account_mapping", ConfigEntity::getDb(), $configDataRow, "is_deleted=0 and account_app_id=:appId", [":appId" => $oldAppId]);
        } else {
            SqlHelper::insert("wechat_account_mapping", ConfigEntity::getDb(), $configDataRow);
        }
        
        // 公众号表－租户库
        $accountDataRow['id'] = $accountDataRow['id']?:StringHelper::uuid();
        SqlHelper::update('p_account', EntityBase::getDb(), $accountDataRow, "id=:id", [":id" => $accountDataRow["id"]]);
        return $accountDataRow['id'];    
    }
    
    /**
     * 保存公众号信息
     * @param object $account
     * @param array $appList
     * @throws \Exception
     * @return bool
     */
    public function insertAccount($account, $appList)
    {
        $conn = PAccount::getDb();
        $transaction = $conn->beginTransaction();

        try {
            $oldAccount = $this->getAccountById($account->id);
            if (!$oldAccount) {
                $conn->createCommand()->insert('p_account', $account->toArray())->execute();

                //新增应用
                //$result = $this->insertAccountApp($appList, $account, $conn);

                //if (!$result) {
                //    $transaction->rollBack();
                //    return false;
                //}

                $transaction->commit();
                return true;

            } else {
                $result = $this->updateAccount($account, $appList, $conn);

                if (!$result) {
                    $transaction->rollBack();
                    return false;
                }

                $transaction->commit();
                return true;
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }

    /**
     * 更新公众号信息
     * @param object $account
     * @param array $appList
     * @param object $conn
     * @throws \Exception
     * @return bool
     */
    public function updateAccount($account, $appList, $conn)
    {
        $data = ['name' => $account->name
            , 'original_id' => $account->original_id
            , 'wechat_number' => $account->wechat_number
            , 'type' => $account->type
            , 'app_id' => $account->app_id
            , 'app_secret' => $account->app_secret 
            , 'mch_id' => $account->mch_id
            , 'mch_key' => $account->mch_key];


        SqlHelper::update('p_account', $conn, $data, ['id' => $account->id]);

        //新增应用
        //$result = $this->insertAccountApp($appList, $account, $conn);

        //if (!$result) {
        //   return false;
        //}

        return true;
    }

    /**
     * 修改公众号信息
     * @param string $id
     * @param string $column
     * @param string $value
     * @throws \Exception
     * @return bool
     */
    public function updateAccountInfo($id, $column, $value)
    {
        $conn = PAccount::getDb();

        $data = [$column => $value];
        try {
            SqlHelper::update('p_account', $conn, $data, ['id' => $id]);
            return true;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 修改公众号应用信息
     * @param string $id
     * @param array $appList
     * @throws \Exception
     * @return bool
     */
    public function updateApps($id, $appList)
    {
        $conn = PAccount::getDb();
        $transaction = $conn->beginTransaction();

        $account = PAccount::find()
            ->where(["is_deleted" => 0])
            ->andWhere(["=", 'id', $id])
            ->select('*')
            ->one();
        try {
            //修改应用
            $result = $this->insertAccountApp($appList, $account, $conn);

            if (!$result) {
                $transaction->rollBack();
                return false;
            }

            $transaction->commit();
            return true;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 删除公众号信息
     * @param string $id
     * @throws \Exception
     * @return bool
     */
    public function removeAccountInfo($id)
    {
        $conn = PAccount::getDb();

        $data = ['is_deleted' => 1];
        try {
            SqlHelper::update('p_account', $conn, $data, ['id' => $id]);
            //$conn->createCommand()->update("p_account_app", ['is_deleted' => 1], ['account_id' => $id])->execute();
            return true;

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 保存公众号对应应用
     * @param $appList
     * @param $account
     * @param $conn
     * @return bool
     */
//    public function insertAccountApp($appList, $account, $conn)
//    {
//        $conn->createCommand()->update("p_account_app", ['is_deleted' => 1], ['account_id' => $account->id])->execute();
//        if (count($appList) > 0) {
//            foreach ($appList as $row) {
//                $accountApp = new PAccountApp();
//                $accountApp->id = StringHelper::uuid();
//                $accountApp->account_id = $account->id;
//                $accountApp->app_code = $row->app_code;
//
//                $accountApp->created_on = $account->created_on;
//                $accountApp->modified_on = $account->modified_on;
//                $accountApp->created_by = $account->created_by;
//                $accountApp->modified_by = $account->modified_by;
//                $accountApp->is_deleted = 0;
//
//                $result = $conn->createCommand()->insert("p_account_app", $accountApp->toArray())->execute();
//                if (!$result) {
//                    return false;
//                }
//            }
//        }
//        return true;
//    }

    /**
     * 查询公众号信息列表
     * @param string $keyword
     * @return array
     */
    public function queryAccountInfo($corpId, $keyword)
    {
        $query = (new \yii\db\Query())
            ->select('p_account.id,p_account.name,p_account.original_id,p_account.wechat_number,p_account.type,p_account.app_id,p_account.app_secret,p_account.token,t_organization.name as corp_name')
            ->from('p_account') 
            ->where('p_account.is_deleted=0');

        if (!empty($keyword)) {
            $query->andWhere(['or', ["like", "name", $keyword], ["like", "wechat_number", $keyword]]);
        }

//        if (!empty($corpId) && $corpId != SUPER_ORGANIZATION_ID) {
//            $query->andWhere(["=", "p_account.corp_id", $corpId]);
//        }

        $connection = PAccount::getDb();
        $command = $query->createCommand($connection);
        $rows = $command->queryAll();

        for ($i = 0; $i < count($rows); $i++) {
            $accountId = $rows[$i]["id"];
            //$apps = $this->getAppsByAccountId($accountId);
            $apps = [];

            $rows[$i]["selected_apps"] = $apps;
        }

        $organization = new Organization();
        $orgs = $organization->searchAllLeafOrgs();

        $data = [];

        $superOrgRow = [];
        $superOrg = $organization->getOrganization(SUPER_ORGANIZATION_ID);
        $superOrgRow["id"] = SUPER_ORGANIZATION_ID;
        $superOrgRow["name"] = $superOrg["name"];
        $superOrgRow["type"] = 'group';
        $superOrgRow["wechat_data"] = [];

        foreach ($rows as $row) {
            if ($row["corp_id"] == SUPER_ORGANIZATION_ID) {
                $superOrgRow["wechat_data"][] = $row;
            }
        }

        if ($corpId == SUPER_ORGANIZATION_ID) {
            $data[] = $superOrgRow;
        }

        foreach ($orgs as $org) {
            $orgRow = [];
            if ($org["id"] != SUPER_ORGANIZATION_ID) {
                $orgRow["id"] = $org["id"];
                $orgRow["name"] = $org["name"];
                $orgRow["type"] = 'corp';
                $orgRow["wechat_data"] = [];
                foreach ($rows as $row) {
                    if ($row["corp_id"] == $org["id"]) {
                        $orgRow["wechat_data"][] = $row;
                    }
                }
                if (count($orgRow["wechat_data"]) > 0) {
                    $data[] = $orgRow;
                }
            }
        }

        return $data;
    }

    /**
     * 根据公众号ID获取公众号信息
     * @param $id
     * @return array|bool
     */
    public function getAccountById($id)
    {
        $query = (new \yii\db\Query())
            ->select('p_account.id,p_account.name,p_account.original_id,p_account.mch_ssl_cert,p_account.mch_ssl_key,p_account.wechat_number,p_account.type,p_account.app_id,p_account.app_secret,p_account.mch_id,p_account.mch_key,p_account.token ')
            ->from('p_account') 
            ->where('p_account.is_deleted=0')
            ->andWhere(['=', 'p_account.id', $id]);

        $connection = PAccount::getDb();
        $command = $query->createCommand($connection);
        $rows = $command->queryOne();
        return $rows;
    }

    public function getWeChatOriginalId($id)
    {
        $query = (new \yii\db\Query())
            ->select('original_id')
            ->from('p_account')
            ->where('p_account.is_deleted=0')
            ->andWhere(['=', 'p_account.id', $id]);

        $connection = PAccount::getDb();
        $command = $query->createCommand($connection);
        $rows = $command->queryOne();
        $we_chat_number = null;
        if (isset($rows)) {
            $we_chat_number = $rows["original_id"];
        }
        return $we_chat_number;
    }

    public function getAccountByOriginalId($accountId, $originalId)
    {
        $query = (new \yii\db\Query())
            ->select('id,original_id')
            ->from('p_account')
            ->where('is_deleted=0')
            ->andWhere(['=', 'original_id', $originalId])
            ->andWhere(['<>', 'id', $accountId]);

        $connection = PAccount::getDb();
        $command = $query->createCommand($connection);
        $rows = $command->queryOne();

        return $rows;
    }

    /**
     * 根据公司Id获取公众号信息
     * @param $corpId
     * @return array|bool
     */
    public function getAccountByCorpId($corpId)
    {
        $query = (new \yii\db\Query())
            ->select('p_account.id,p_account.name,p_account.original_id,p_account.wechat_number,p_account.type,p_account.app_id,p_account.app_secret,p_account.corp_id,p_account.token')
            ->from('p_account')
            ->where('p_account.is_deleted=0')
            ->andWhere(['=', 'p_account.corp_id', $corpId]);

        $connection = PAccount::getDb();
        $command = $query->createCommand($connection);
        $rows = $command->queryAll();
        return $rows;
    }

    public function getAllAccount()
    {
        $query = (new \yii\db\Query())
            ->select('id,name,corp_id')
            ->from('p_account')
            ->where('is_deleted=0');

        $connection = PAccount::getDb();
        $command = $query->createCommand($connection);
        $rows = $command->queryAll();
        return $rows;
    }

    /**
     * 根据公众号ID获取应用
     * @param $id
     * @return array
     */
//    public function getAppsByAccountId($id)
//    {
//        $query = (new \yii\db\Query())
//            ->select('p_account_app.id,p_account_app.account_id,p_account_app.app_code,t_app.app_name,t_app.icon_url')
//            ->from('p_account_app')
//            ->innerJoin("t_app", "p_account_app.app_code=t_app.app_code")
//            ->where('p_account_app.is_deleted=0')
//            ->andWhere(['=', 'p_account_app.account_id', $id]);
//
//        $connection = PAccountApp::getDb();
//        $command = $query->createCommand($connection);
//        $rows = $command->queryAll();
//        return $rows;
//    }

//    /**
//     * 根据快捷入口数据
//     * @param $tenantCode
//     * @return array|bool
//     */
//    public function getEntry($tenantCode)
//    {
//        $query = (new \yii\db\Query())
//            ->select('tenant.code AS tenant_code,authorization.application_id,application.app_name,function.*,site.`url` AS site_url')
//            ->distinct()
//            ->from('tenant')
//            ->innerJoin('contract', 'tenant.id=contract.`tenant_id`')
//            ->innerJoin('authorization', 'contract.id=authorization.`contract_id`')
//            ->innerJoin('application', 'authorization.`application_id` = application.id')
//            ->innerJoin('function', 'application.`app_code` = function.app_code')
//            ->innerJoin('site', 'site.site_code = function.provideby_site_code')
//            ->where('function.is_deleted=0')
//            ->andWhere(['=', 'tenant.code', $tenantCode])
//            ->orderBy('function.name ASC');
//
//        $command = $query->createCommand(\yii::$app->db);
//        $rows = $command->queryAll();
//        return $rows;
//    }

    /**
     * 获取所有微网站url
     * @return array
     */
    public function getMicroSites()
    {
        $query = (new \yii\db\Query())
            ->select('id,site_code,site_name,url')
            ->from('site')
            ->where("is_deleted = 0")
            ->andWhere("FIND_IN_SET('微网站', type)>0");

        $command = $query->createCommand(\yii::$app->db);
        $rows = $command->queryAll();
        return $rows;
    }
}
