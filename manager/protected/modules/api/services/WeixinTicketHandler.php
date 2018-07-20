<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\api\services;

use app\framework\db\SqlHelper;
use app\framework\db\ConfigEntity;

/**
 * 微信第三方开放平台消息
 *
 * @author chenxy
 */
class WeixinTicketHandler extends \app\framework\weixin\msg\BaseComponentHandler
{
    public function __construct($userData = [])
    {
        parent::__construct($userData);
    }
    
    /**
     * 推送component_verify_ticket
     * @param array $data
     * @return string
     */
    public function component_verify_ticket($data)
    {
        // 更新第三方平台的ticket
        $appId = $data['AppId'];
        $ticket = $data['ComponentVerifyTicket'];
        $accessTokenRepository = \Yii::$container->get('app\framework\weixin\component\IComponentAccessTokenRepository');
        $accessTokenRepository->updateVerifyTicket($appId, $ticket);
        return '';
    }
    
    /**
     * 推送取消授权通知
     * @param array $data
     * @return string
     */
    public function unauthorized($data)
    {
        $appId = $data['AuthorizerAppid'];
        $msgTime = $data['CreateTime'];
        $tenantCode = \app\framework\weixin\helper\BizTenantCodeHelper::getTenantCodeByAppId($appId);
        // 找不到对应的租户代码时，直接返回
        if (!$tenantCode) {
            return '';
        }
        $dbConn = \app\framework\biz\cache\OrganizationCacheManager::getTenantDbConn($tenantCode);
        // 清除缓存
        \app\framework\weixin\DbAccessTokenRepository::clearCache($appId);
        \app\framework\weixin\helper\BizTenantCodeHelper::clearTenantAppIdMappingCache($appId);
        // 删除映射
        SqlHelper::update("wechat_account_mapping", ConfigEntity::getDb(), ['is_deleted' => 1], "account_app_id=:appId", [":appId" => $appId]);
         // 更新公众号授权状态为未授权
        $dbConn->createCommand()->update(
            'p_account',
            ["is_authed" => 0, "unauth_time" => date("Y-m-d H:i:s", intval($msgTime)), "authorized_privilege_set" => ''],
            "app_id=:id and is_deleted=0",
            [':id' => $appId]
        )->execute();
        return '';
    } 
}
