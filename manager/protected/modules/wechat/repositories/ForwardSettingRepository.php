<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\wechat\repositories;

use app\modules\RepositoryBase;
use yii\db\Query;
use app\framework\utils\StringHelper;
use app\framework\utils\DateTimeHelper;

/**
 * Description of ForwardSettingRepository
 *
 * @author Chenxy
 */
class ForwardSettingRepository extends RepositoryBase
{
    /**
     * 获取所有消息通知设置
     * @param string $accountId
     * @return array
     */
    public function getSettings($accountId)
    {
        $query = new Query();
        $rows = $query->from('p_forward_setting')
                ->select("id, partner_name, url, token, secret_key, account_id")
                ->where("account_id =:account_id and is_deleted = 0", [':account_id' => $accountId])
                ->orderBy("modified_on desc")
                ->all($this->getTenantDb());
        
        return $rows;
    }
    
    /**
     * 删除设置
     * @param string $id
     * @return int
     */
    public function removeSetting($id, $delete_by)
    {
        $db = $this->getTenantDb();
        return $db->createCommand()->update('p_forward_setting', ["is_deleted" => 1, 'modified_by' => $delete_by, 'modified_on' => DateTimeHelper::now()], ["id" => $id])->execute();
    }
    
    /**
     * 增加设置
     * @param string $accountId
     * @param string $name
     * @param string $url
     * @param string $token
     * @param string $secretKey
     * @param string $createdBy
     * @return int
     */
    public function addSetting($accountId, $name, $url, $token, $secretKey, $createdBy)
    {
        $db = $this->getTenantDb();
        $now = DateTimeHelper::now();
        return $db->createCommand()->insert(
            'p_forward_setting',
            ['id' => StringHelper::uuid(),
            'account_id' => $accountId,
            'partner_name' => $name,
            'url' => $url,
            'token' => $token,
            'secret_key' => $secretKey,
            'created_by' => $createdBy,
            'created_on' => $now,
            'modified_by' => $createdBy,
            'modified_on' => $now,
            'is_deleted' => 0]
        )->execute();
    }
    
    /**
     * 更新转发设置
     * @param string $id
     * @param array $data
     * @return int
     */
    public function updateSetting($id, $data)
    {
        $db = $this->getTenantDb();
        return $db->createCommand()->update('p_forward_setting', $data, ["id" => $id])->execute();
    }
    
    /**
     * 获取转发设置
     * @param type $id
     * @return type
     */
    public function findSetting($id)
    {
        $query = new Query();
        $row = $query->from('p_forward_setting')
                ->select("id, partner_name, url, token, secret_key, account_id")
                ->where("id=:id", [':id' => $id])
                ->createCommand($this->getTenantDb())
                ->queryOne();
        
        return $row;
    }
}
