<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\wechat\services;

use app\framework\utils\DateTimeHelper;
use app\modules\ServiceBase;
use app\modules\wechat\repositories\MassMessageRepository;
use app\modules\wechat\repositories\MaterialRepository;
use app\framework\weixin\AccessTokenHelper;
use app\modules\wechat\repositories\AccountRepository;
use app\framework\weixin\proxy\fw\MassMessage;
use app\framework\weixin\proxy\fw\MassPreview;

/**
 * Description of MassMessageService
 *
 * @author Chenxy
 */
class MassMessageService extends ServiceBase
{
    private $_massMessageRepository;
    private $_accountRepository;
    private $_materialRepository;
    private $_weixinAccessorProvider;

    // 集团id
    const GROUP_ID_STRING = '11b11db4-e907-4f1f-8835-b9daab6e1f23';
    
    public function __construct(
        MassMessageRepository $massMessageRepository,
        MaterialRepository $materialRepository,
        AccountRepository $accountRepository)
    {
        $this->_massMessageRepository = $massMessageRepository;
        $this->_materialRepository = $materialRepository;
        $this->_accountRepository = $accountRepository;
        if (\Yii::$container->has('app\modules\wechat\services\WeixinAccessorProvider')) {
            $this->_weixinAccessorProvider = \Yii::$container->get('app\modules\wechat\services\WeixinAccessorProvider', [$accountRepository]);
        } else {
            $this->_weixinAccessorProvider = new WeixinAccessorProvider($accountRepository);
        }
    }

    /**
     * 群发消息预览，发送预览信息到指定手机号
     * @param string $accountId
     * @param string $msgType
     * @param string $mediaId
     * @param string $toMobile
     * @param string $userId
     * @return mixed
     * @throws \Exception
     * @throws \app\framework\webService\Exceptions\NotImplementedException
     */
    public function previewByMobile($accountId, $msgType, $mediaId, $toMobile, $userId)
    {
        // 获取手机号对应的粉丝数据
        $fanInfo = $this->getFollowedFanInfo($accountId, $toMobile);
        if (empty($fanInfo)) {
            throw new \Exception("该手机号码匹配不到微信预览用户");
        }

        //保存预览人信息
        $fanInfo['mobile'] = $toMobile;
        $fanInfo['media_id'] = $mediaId;
        $this->_massMessageRepository->savePreviewHistory($userId, $fanInfo);

        $openId = $fanInfo['openid'];
        return $this->preview($accountId, $msgType, $mediaId, $openId);
    }

    public function getPreviewHistory($userId,$accountId, $cnt = 12)
    {
        $list = $this->_massMessageRepository->getPreviewHistory($userId,$accountId, $cnt);
        foreach ($list as $k=>$v) {
            if (!$v['name']) {
                $list[$k]['name'] = $v['nick_name'];
            }
            unset($list[$k]['nick_name']);
        }

        return $list;
    }
    
    /**
     * 根据用户注册的手机号码获取关注的粉丝信息 
     * @param type $mobile
     * @return type
     */
    public function getFollowedFanInfo($accountId, $mobile)
    { 
        $fanInfo = $this->_massMessageRepository->getFollowedFanInfo($accountId, $mobile);
        return $fanInfo ?: null;
    }

    /**
     * 群发消息预览
     * @param type $corpId
     * @param type $msgType
     * @param type $mediaId
     * @param type $openId
     * @return type
     * @throws \app\framework\webService\Exceptions\NotImplementedException
     */
    private function preview($accountId, $msgType, $mediaId, $openId)
    {    
        $apiProxy = $this->getWeixinMassPreviewProxy($accountId);
        switch (strtolower($msgType)) {
            case 'mpnews':
                $apiReturn = $apiProxy->mpnews($openId, $mediaId);
                break;
            case 'image':
                $apiReturn = $apiProxy->image($openId, $mediaId);
                break;
            case 'voice':
                $apiReturn = $apiProxy->voice($openId, $mediaId);
                break;
            case 'video':
                $apiReturn = $apiProxy->video($openId, $mediaId);
                break;
            default :
                throw new \app\framework\webService\Exceptions\NotImplementedException("消息内容类型{$msgType}暂不支持");
        }
        
        return $apiReturn->msg_id;
    }
    
    /**
     * 获取群发审批人，成功返回审批人member_id失败返回''
     * @param type $accountId
     * @return type
     */
    public function getMassMsgApprover($accountId)
    {
        return $this->_massMessageRepository->getMassMsgApprover($accountId);
    }

    /**
     * 提交保存
     * @param string $id
     * @param string $accountId 
     * @param string $sendMemberLevelIds
     * @param string $msgType
     * @param string $mediaId
     * @param string $userType
     * @param string $operatorId
     * @return string
     */
    public function save($id, $accountId, $sendMemberLevelIds,$userType, $msgType, $mediaId,  $operatorId)
    {
        // 采用新增后插，先删除原有失败提交（可能二维码生成失败，造成此次群发无法审批）
        if (!empty($id)) {
            $this->_massMessageRepository->deleteMassMsg($id);
        }
        
        // 插入
        $displayInfo = $this->getDisplayInfo($msgType, $mediaId);
        $data = [
            'id' => $id ?: \app\framework\utils\StringHelper::uuid()
           ,'account_id' => $accountId  
           ,'object_type' => $userType  
           ,'member_level_ids' => $sendMemberLevelIds
           ,'mpnews_cover_url' => $displayInfo['cover_url']
           ,'mpnews_title' => $displayInfo['title']
           ,'mpnews_summary' => $displayInfo['summary']
           ,'msg_type' => $msgType
           ,'media_id' => $mediaId
           ,'status' => '已保存'
           ,'total_count' => 0
           ,'filter_count' => 0
           ,'sent_count' => 0
           ,'error_count' => 0
           ,'created_by' => $operatorId
           ,'modified_by' => $operatorId
           ,'is_deleted' => 0
        ];
         
        $this->_massMessageRepository->insertMassMsg($data);
        return $data['id'];
    }

    /**
     *
     * @param string $corpId 当前公司id
     * @param string $massMsgId 群发消息id
     * @param string $operatorId 当前用户id
     * @return array
     * @throws \Exception
     */
    public function send($accountId, $massMsgId, $operatorId)
    {
        \Yii::trace('群发消息:开始发送服务');

        // 校验
        $massMsgInfo = $this->_massMessageRepository->findMassMsg($massMsgId, ['id','account_id','object_type', 'status',  'member_level_ids',  'total_count', 'media_id', 'msg_type']);
        if ($massMsgInfo === false) {
            throw new \Exception("消息数据可能已被删除，请刷新后重试");
        }

        //审批中 改为 审批通过 才允许发送群发消息
        if ($massMsgInfo['status'] != '审批通过') {
            throw new \Exception("审批通过后才允许发送");
        } 
        
        $users = $this->getPreSendUser($accountId,  $massMsgInfo['member_level_ids'],$massMsgInfo['object_type']);
        // 分批处理,每次发送最大9999个用户
        $totalUsers = count($users);

        if ($totalUsers < 2) {
            throw new \Exception("群发当前满足条件的发送对象少于2人，不能发送");
        }
        
        //测试信息
        if (YII_DEBUG && $totalUsers < 100) {
            \Yii::trace('群发消息: 全部待接收用户 ' . json_encode($users));
        }

        if (!isset(\Yii::$app->params['wx_sending_massMessage_count_per'])) {
            throw new \Exception('缺少params参数项wx_sending_massMessage_count_per');
        }
        $maxSendCount = \Yii::$app->params['wx_sending_massMessage_count_per'];
        $batchFromIndex = 0;
        $isFirstBatch = false;
        if ($massMsgInfo['total_count'] == 0) {
            $massMsgInfo['total_count'] = $totalUsers;
            $isFirstBatch = true;
        }
        
        /* 避免大数量时超时的问题暂时不做发送对象明细及月次数处理该代码移到上面，以减少不必要的处理
        // 发送全部粉丝
        if ($this->isSendAllFans($isGroupUser, $massMsgInfo['object_type'], $corpId)) {
            $massMsgBatchId = $this->internalBatchSend($accountId, true, $users, $massMsgInfo, $operatorId, true);
            $batchLogIds[] = $massMsgBatchId;
            return $batchLogIds;
        }
        */
        
        // 发送部分粉丝
        //$msg = "corpId:{$corpId},massMsgId:{$massMsgId},totolUsers:{$totalUsers},isGroupUser:{$isGroupUser},accountId:{$accountId},object_corp_ids:" . $massMsgInfo['object_corp_ids'];
        $batchLogIds = [];
        while ($totalUsers > 0) {
            $batchLength = $totalUsers > $maxSendCount ? $maxSendCount : $totalUsers;
            $msg = "初始化批长度{$batchLength}";
            // 保证最后一批不少于2人
            $batchLength = ($totalUsers == $maxSendCount + 1) ? ($totalUsers) : $batchLength;
            $msg .= "计算后批长度{$batchLength}";
            $batchUsers = array_slice($users, $batchFromIndex, $batchLength);
            $msg .= json_encode($batchUsers);
            \Yii::trace("群发消息: 数据跟踪{$msg}");
            if (YII_DEBUG && $totalUsers < 100) {
                \Yii::trace('群发消息: 分批接收用户 ' . json_encode($batchUsers));
            }

            $massMsgBatchId = $this->internalBatchSend($accountId, $isFirstBatch, $batchUsers, $massMsgInfo, $operatorId, false);
            $batchLogIds[] = $massMsgBatchId;
            $totalUsers -= $batchLength;
            $batchFromIndex += $batchLength;
        }
        
        return $batchLogIds;
    }

    /**
     * @param $accountId
     * @param $isFirstBatch
     * @param $users
     * @param $massMsgInfo
     * @param $senderId
     * @return mixed
     * @throws \Exception
     * @throws \app\framework\webService\Exceptions\NotImplementedException
     */
    private function internalBatchSend($accountId, $isFirstBatch, $users, $massMsgInfo, $senderId, $isToAll)
    {
        if (!$isToAll && count($users) < 2) {
            throw new \Exception("群发当前满足条件的发送对象少于2人，不能发送");
        }
        
        if (!$isToAll && count($users) > 10000) {
            throw new \Exception("群发当前满足条件的发送对象超过10000人，不能发送");
        }
        
        // 获取所有发送对象的openid
        $openIds = [];
        if (!$isToAll) {
            foreach ($users as $u) {
                $openIds[] = $u['openid'];
            }
        }

        //去重处理, 通过mediaId 和 openIds 查询是否有批量发送记录
        $openIdsStr = "";
        if (!$isToAll) {
            $openIdsStr = implode(",", $openIds);
        }
        $oldMsgMassBatch = $this->_massMessageRepository->getMassMsgBatch($massMsgInfo['media_id'], $openIdsStr);
        if ($oldMsgMassBatch) {
            throw new \Exception("该群发消息批量发送已存在，不能重复发送");
        }
        //插入批量发送提交中状态的记录
        $batchId = \app\framework\utils\StringHelper::uuid();
        $msgMassBatchRowData = ['id' => $batchId,'mass_msg_id' => $massMsgInfo['id'], 'status' => '提交中', 'send_time' => DateTimeHelper::now()
            ,'msg_id' => '', 'openids' => $openIdsStr, 'total_count' => count($users), 'filter_count' => 0, 'sent_count' => 0, 'error_count' => 0
            ,'created_by' => $senderId, 'modified_by' => $senderId, 'is_deleted' => 0];
        $this->_massMessageRepository->insertMassMsgBatch($msgMassBatchRowData);

        try {
            $msgId = $this->massSend($accountId, $openIds, $massMsgInfo['media_id'], $massMsgInfo['msg_type'], $isToAll);
        } catch (\Exception $ex) {
            //提交微信失败 删除该批量发送记录
            $this->_massMessageRepository->deleteMassMsgBatch($batchId);
            throw $ex;
        }
        
        if (empty($msgId)) {
            //msgId异常 删除该批量发送记录
            $this->_massMessageRepository->deleteMassMsgBatch($batchId);
            throw new \Exception("msgId为空异常，数据：公众号id {$accountId},素材media_id " . $massMsgInfo['media_id']);
        }
        // 发送表
        $now = DateTimeHelper::now();
        $msgMassRowData = [];
        if ($isFirstBatch) {
            $msgMassRowData = ['status' => '发送中', 'send_time' => $now, 'modified_by' => $senderId
                ,'total_count' => $massMsgInfo['total_count'], 'filter_count' => 0, 'sent_count' => 0, 'error_count' => 0];
        }
       
        // 更新批发送状态为发送中
        $msgMassBatchRowData = ['id' => $batchId,'status' => '发送中', 'msg_id' => $msgId, 'send_time' => $now];
       
        /* 避免大数量时超时的问题暂时不做发送对象明细及月次数处理
        // 发送明细
        $fanIds = array_column($users, 'id');
        $fanMassMsgPushRows = $this->_massMessageRepository->getFanMassMsgPushByInIds($fanIds);
        $massSendUsers = [];
        foreach ($users as $u) {
            $massSendUsers[$u['id']] = [
                'fan_id' => $u['id']
              , 'member_id' => $u['member_id']
              , 'id' => \app\framework\utils\StringHelper::uuid()
              , 'mass_msg_id' => $massMsgInfo['id']
              , 'batch_id' => $msgMassBatchRowData['id']
              ,'has_send' => array_key_exists($u['id'], $fanMassMsgPushRows) 
                ? (!$fanMassMsgPushRows[$u['id']]['isOutFourTimes']) 
                : 0
              ,'is_deleted' => 0
              ,'created_by' => $senderId
              , 'modified_by' => $senderId];
        }
        */
        $massSendUsers = [];
        $fanMassMsgPushRows = [];
        $this->_massMessageRepository->insertBatchMassMsg($massMsgInfo['id'], $msgMassRowData, $msgMassBatchRowData, $massSendUsers, $fanMassMsgPushRows);
        return $msgMassBatchRowData['id'];
    }

    /**
     * 获取群发日志列表数据
     * @param string $accountId 公众号id
     * @param $offset
     * @param $limit
     * @return array ['total' => 总记录数
     * [
     * ['id' =>日志id ,'thumb'=>缩略图,'object_type' =>发送对象 ,'title' =>标题 ,'description' =>描述
     * ,'status' => 状态,'send_time' => 发送时间,
     * 'total_count'=>总发送人数,'filter_count'=>实际发送人数,'sent_count'=>成功发送人数,'error_count'=>失败人数]
     * ],....
     * ]
     * @internal param string $msgType 目前只支持图文mpnews
     */
    public function getMassMsgLogList($accountId, $offset, $limit)
    {
        $row = $this->_massMessageRepository->getMassMsgLog($accountId, $offset, $limit);
        return ['total' => $row['total'], 'items' => $row['data']];
    }

    /**
     * 撤消群发消息
     * @param type $corpId
     * @param type $massMsgId
     * @param type $operatorId
     * @return type
     * @throws \Exception
     */
    public function cancelMassMsg($accountId, $massMsgId, $operatorId)
    {
        // 查找msg_id
        $row = $this->_massMessageRepository->findMassMsg($massMsgId);
        if ($row === false) {
            throw new \Exception("找不到群发日志记录,id:{$massMsgId}");
        }
        
        if ($row['status'] != '发送成功') {
            throw new \Exception("只有发送成功的消息才能撤消，请刷新后重试");
        }
        
        // 调用接口撤消
        $msgIds = $this->_massMessageRepository->getMsgIdByMassId($massMsgId); 
        $apiProxy = $this->getWeixinMassMessageProxy($accountId);
        foreach ($msgIds as $msgId) {
            $apiProxy->delete($msgId['msg_id']);
        }
        
        // 撤消成功删除对应图文素材进行重建（注意：暂只对图文，其它类型需要查看微信开发者文档）
        $this->rebuildMpnews($accountId, $row['media_id'], $operatorId);
        
        // 更新记录
        return $this->_massMessageRepository->cancelMassMsg($massMsgId, $operatorId);
    }

    /**
     * 查找群发日志记录
     * @param $massMsgId
     * @param string|array $fields
     * @return array|bool
     */
    public function findMassMsg($massMsgId, $fields = '*')
    {
        $row = $this->_massMessageRepository->findMassMsg($massMsgId, $fields);
        return $row;
    }

    /**
     * 查找公众号
     * @param $accountId
     * @return array row
     */
    public function getAccountByAccountId($accountId)
    {
        $row = $this->_massMessageRepository->getAccountByAccountId($accountId);
        return $row;
    }

    /**
     * 查找粉丝信息
     * @param $accountId
     * @return array row
     */
    public function getFanByFanId($fanId)
    {
        $row = $this->_massMessageRepository->getFanByFanId($fanId);
        return $row;
    }
    
    /**
     * 重建图文，撤消群发微信会删除对应的图文素材
     * @param type $accountId
     * @param type $mediaId
     * @param type $operatorId
     * @return type
     */
    private function rebuildMpnews($accountId, $mediaId, $operatorId)
    {
        $materialService = new MaterialService($this->_materialRepository, $this->_accountRepository);
        $mpnewsInfo = $this->_materialRepository->getMpnewsInfoByMediaId($mediaId);
        // 本地图文已删除则不再重建
        if ($mpnewsInfo === false) {
            return;
        }
        // 增加一个同样的图文并上传微信端
        $mpArticles = $materialService->getArticlesById($mpnewsInfo['id']);
        //[{'title':标题,'cover_url':封面图片url,'cover_name':封面图片文件名（含扩展名）,'is_cover_showin_body':1显示封面0不显示,
        //'body':正文,'summary':摘要,'original_url':原文url,'author':作者},多图文有多个]
        //id, title, author, cover_url, is_cover_showin_body, summary, body, original_url
        $newsData = [];
        foreach ($mpArticles as $a) {
            $newsData[] = [
                'title' => $a['title']
               ,'cover_url' => $a['cover_url']
               ,'cover_name' => '' // 重建时图片不会重新上传，故这里不需要传值
               ,'is_cover_showin_body' => $a['is_cover_showin_body']
               ,'body' => $a['body']
               ,'summary' => $a['summary']
               ,'original_url' => $a['original_url']
               ,'author' => $a['author']
            ];
        }
        $materialService->addMpnews($accountId, $newsData, $operatorId);
        // 删除本地图文
        $this->_materialRepository->removeMpnewsByMediaId($mediaId, $operatorId);
    }

    private function getDisplayInfo($msgType, $mediaId)
    {
        switch ($msgType) {
            // [title=>标题,author=>作者，cover_url=>封面url,summary=>摘要]
            case 'mpnews':
                $row = $this->_materialRepository->getFirstArticle($mediaId);
                if ($row === false) {
                    return ['title' => '', 'author' => '', 'cover_url' => '', 'summary' => ''];
                }
                break;
            default :
                throw new \app\framework\webService\Exceptions\NotImplementedException("不支持的消息类型{$msgType}");
        }
        
        return $row;
    }


    private function massSend($accountId, $openIds, $mediaId, $msgType, $isToAll)
    {
        // 调用接口发送
        $apiProxy = $this->getWeixinMassMessageProxy($accountId);
        switch ($msgType) {
            case 'mpnews':
                $mpnewsInfo = $this->_materialRepository->getMpnewsInfoByMediaId($mediaId);
                if ($mpnewsInfo === false) {
                    throw new \Exception("图文的media_id不存在，可能已被删除");
                }
                if ($isToAll) {
                    \Yii::trace('群发消息:发送给全部粉丝');
                    $apiReturn = $apiProxy->sendMpnewsToAll($mediaId);
                } else {
                    \Yii::trace('群发消息:按指定openid发送');
                    $apiReturn = $apiProxy->sendMpnews($openIds, $mediaId);
                }
                break;
            default :
                throw new \app\framework\webService\Exceptions\NotImplementedException("消息内容类型{$msgType}暂不支持");
        }
        
        return $apiReturn->msg_id;
    }

    /**
     * 获取发送对象
     * @param string $accountId 
     * @param string $levelIds
     * @param string $userType
     * @return array
     * @throws \InvalidArgumentException
     */
    private function getPreSendUser($accountId, $levelIds, $userType)
    {
        switch ($userType) {
            case '粉丝':
                $users = $this->_massMessageRepository->getFans($accountId); 
                break;
            case '会员':
                $users = $this->_massMessageRepository->getMembers($accountId, $levelIds);
                break; 
            default:
                throw new \InvalidArgumentException("参数userType无效");
        }
       
        return $users;
    } 
    /**
     * 获取群发微信接口代理
     * @param string $accountId
     * @return MassMessage
     * @throws \Exception
     */
    private function getWeixinMassMessageProxy($accountId)
    {
        return $this->_weixinAccessorProvider->getWeixinMassMessageProxy($accountId);
    }
    
    private function getWeixinMassPreviewProxy($accountId)
    {
        return $this->_weixinAccessorProvider->getWeixinMassPreviewProxy($accountId);
    }
            
    public function getAllCompany()
    {
        $tReader = \Yii::$container->get('app\framework\biz\tenant\TenantReaderInterface');
        $tenantCode = $tReader->getCurrentTenantCode();
        return \app\framework\biz\cache\OrganizationCacheManager::getAllCompany($tenantCode);
    }
    
            
    
    public function updateStatus($status, $massMsgId)
    {
        return $this->_massMessageRepository->updateMassMsgStatus($status, $massMsgId);
    }

    public function updateStatusByPreviousStatus($status, $massMsgId, $previousStatus)
    {
        return $this->_massMessageRepository->updateMassMsgStatusByPreviousStatus($status, $massMsgId, $previousStatus);
    }

    public function getJssdksignConfig( $accountId, $url)
    {
        $invokeUri = \app\framework\biz\cache\SiteCacheManager::getSiteUrl('PublicAccountSite') . "/index.php?r=api/weixin/jssdksign";
        $restClient = new \app\framework\webService\RestClientHelper();
        \Yii::trace('call api: ' . $invokeUri);
        $signConfig = $restClient->invoke($invokeUri, ['accountId'=>$accountId, 'url'=>$url], 'GET');
        return $signConfig;
    }

    /**
     * 获取业务参数会员等级的所有记录
     * @param author OceanDeng(denghg@mysoft.com.cn)
     * @return array
     */
    public function getMemberLevelList($accountId)
    {
        return $this->_massMessageRepository->getMemberLevelList($accountId);
    }
}
