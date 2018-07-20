<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\wechat\services;

use app\modules\ServiceBase;
use app\modules\wechat\repositories\ForwardSettingRepository;
use app\modules\api\services\BizCacheManager;

/**
 * Description of ForwardSettingService
 *
 * @author Chenxy
 */
class ForwardSettingService extends ServiceBase
{
    private $_forwardSettingRepository;
    
    public function __construct(ForwardSettingRepository $forwardSettingRepository)
    {
        $this->_forwardSettingRepository = $forwardSettingRepository;
    }
    
    /**
     * 获取转发列表
     * @param string $accountId
     * @return array
     */
    public function getForwardSettingList($accountId)
    {
        return $this->_forwardSettingRepository->getSettings($accountId);
    }
    
    /**
     * 保存转发设置
     * @param string $accountId
     * @param string $id
     * @param string $name
     * @param string $url
     * @param string $token
     * @param string $secretKey
     * @param string $userId
     * @return int
     */
    public function saveForwardSetting($accountId, $id, $name, $url, $token, $secretKey, $userId)
    {
        // 清除缓存
        BizCacheManager::clearAccountMsgForwardSettingCache($accountId);
        if (empty($id)) {
            return $this->_forwardSettingRepository->addSetting($accountId, $name, $url, $token, $secretKey, $userId);
        }
        
        return $this->_forwardSettingRepository->updateSetting($id, ['partner_name'=>$name, 'url'=>$url, 'token'=>$token, 'secret_key' => $secretKey, 'modified_by'=>$userId]);
    }
    
    /**
     * 删除转发设置
     * @param string $id
     * @param string $userId
     * @return int
     */
    public function deleteForwardSetting($id, $userId)
    {
        $row = $this->_forwardSettingRepository->findSetting($id);
        if ($row) {
            $accountId = $row["account_id"];
            BizCacheManager::clearAccountMsgForwardSettingCache($accountId);
        }
        
        return $this->_forwardSettingRepository->removeSetting($id, $userId);
    }
    
    /**
     * 获取转发设置信息
     * @param string $id
     * @return array
     */
    public function findForwardSetting($id)
    {
        $row = $this->_forwardSettingRepository->findSetting($id);
        return $row?:[];
    }
}
