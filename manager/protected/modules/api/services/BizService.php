<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\api\services;

/**
 * Description of FansService
 *
 * @author Chenxy
 */
use app\framework\biz\cache\FanCacheManager;
use app\modules\api\repositories\BizRepository; 
use app\framework\weixin\proxy\fw\User; 
use app\framework\weixin\AccessTokenHelper;
use app\framework\weixin\proxy\fw\CustomerMessage;
use app\framework\weixin\inform\MsgInformService;

class BizService
{
    private $_bizRepository;
    private $_dkfRepository;
    // 微信开发者号
    private $_wechat;
    private $_accountId;
    private $_accessTokenRepository;
    private $_accessTokenHelper;

    // 缓存600秒
    private $_cacheExpireTime = 600;
    
    public function __construct(BizRepository $bizRepository, DkfRepository $dkfRepository, $wechat, $accessTokenRepository = null)
    {
        if (is_null($accessTokenRepository)) {
            $accessTokenRepository = \Yii::$container->get('app\framework\weixin\interfaces\IAccessTokenRepository');
        }
        
        if (empty($accessTokenRepository)) {
            throw new \Exception('未注入app\framework\weixin\interfaces\IAccessTokenRepository实例');
        }
        $this->_accessTokenRepository = $accessTokenRepository;
        $this->_accessTokenHelper = new AccessTokenHelper($wechat, $accessTokenRepository);
        $this->_bizRepository = $bizRepository;
        $this->_dkfRepository = $dkfRepository;
        $this->_wechat = $wechat;
        $this->_accountId = $this->_accessTokenRepository->getConfigValue($this->_wechat, 'id');
    }

    /**
     * 添加粉丝
     * @param string $openId
     * @return guid pk
     */
    public function addFans($openId)
    {
        $user = new User($this->_accessTokenHelper);
        $wxUserInfo = $user->info($openId);
        
        $data = [
            'openid' => $wxUserInfo->openid,
            'nick_name' => $wxUserInfo->nickname,
            'sex' => ($wxUserInfo->sex == 1 ? '男' : ($wxUserInfo->sex == 2 ? '女' : '未知')),
            'city' => $wxUserInfo->city,
            'country' => $wxUserInfo->country,
            'province' => $wxUserInfo->province,
            'language' => $wxUserInfo->language,
            'headimg_url' => $wxUserInfo->headimgurl,
            'follow_time' => date("Y-m-d H:i:s", $wxUserInfo->subscribe_time),
            'union_id' => (is_object($wxUserInfo) && property_exists($wxUserInfo, 'unionid')) ? $wxUserInfo->unionid : '',
            'account_id' => $this->_accountId
        ];

        //更新粉丝缓存
        FanCacheManager::removeCache($data['openid']);
        // 查找公众号下粉丝是否已存在
        $isExists = $this->_bizRepository->findFansBy($this->_accountId, $data['openid'], "id");
        if (!$isExists) {
            return $this->_bizRepository->addFans($data);
        }
        
        $data['is_deleted'] = 0;
        $data['is_followed'] = 1;
        return $this->_bizRepository->updateFansInfo($data, $this->_accountId, $data['openid']);
    }
    
    public function updateFansUnfollowTime($unfollowTime, $openId)
    {
        // 查找公众号下粉丝是否已存在
        //更新粉丝缓存
        FanCacheManager::removeCache($openId);
        $isExists = $this->_bizRepository->FindFansBy($this->_accountId, $openId, "id");
        if (!$isExists) {
            $this->addFans($openId);
        }
        
        $data = ['unfollow_time' => $unfollowTime, 'is_followed' => 0];
        return $this->_bizRepository->updateFansInfo($data, $this->_accountId, $openId);
    }
    
    public function getReplyContent($userBehavior, $cacheKey = '', $inputData = '')
    {
        // 使用缓存
        $cache = \Yii::$app->cache;
        if (!empty($cacheKey) && $cache->exists($cacheKey)) {
            $replyContent = $cache->get($cacheKey);
            return $replyContent;
        }

        $id='';
        // 响应用户行为
        switch ($userBehavior) {
            // 关注回复
            case '关注':
                $content =  $this->getWelcomeReplyContent();
                $id = is_array($content) ? $content['id'] : "";
                break;
            // 用户输入一段文字
            case '关键字':
                // 关键字不匹配先尝试走多客服
                $content = $this->getKeywordReplyContent($inputData, false);
                $id = is_array($content) ? $content['id'] : "";
                break;
            // 用户点击菜单按钮，这里的$inputData值为event-key值
            case '菜单':
                $content = $this->getMenuReplayContent($inputData);
                // 支持菜单进入多客服
                if ($content['content-type'] == '转多客服') {
                    return $content;
                }
                
                // 由于对于view菜单也会触发click事件推送，为了匹配p_menu表中的内容，对链接类型进行过滤，见：KFXT-1535
                if ($content['content-type'] == '链接') {
                    $content = false;
                }
                $id = $inputData;
                break;
        }
        
        // 内容未定义
        if (!isset($content) || $content === false || !$content['content-type'] || !$content['content-data']) {
            return false;
        }

        $content['content-data'] = $this->convertReplyContentFormat($content['content-type'], $content['content-data'], $id);
        // 回复内容缓存10分钟
        // 有效的回复内容缓存10分钟
//        if (!empty($cacheKey) && $content !== false) {
        if (!empty($cacheKey)) {
            $cache->set($cacheKey, $content, $this->_cacheExpireTime);
        }
        return $content;
    }
    
    public function sendInformMsg($openId)
    {
        $rows = MsgInformService::search($this->_accountId, $openId);
        $customerMsg = new CustomerMessage($this->_accessTokenHelper);
        foreach ($rows as $row) {
            // 暂支持发送文本消息
            switch ($row['type']) {
                case '文字':
                    $customerMsg->sendText($openId, $row['content']);
                    MsgInformService::remove($row['id']);
                    break;
                default :
                    break;
            }
        }
    }

    /**
     * 验证相应的素材是否存在，若存在返回数据行，不存在返回false
     * @param type $type
     * @param type $content
     * @return type
     * @throws \Exception
     */
    private function isMaterialContent($type, $content)
    {
        $materialContent = json_decode($content);
        if (is_null($materialContent)) {
            throw new \Exception("回复设置{$type}数据内容不是有效的json格式，内容:{$content}");
        }
        
        if (!isset($materialContent->media_id)) {
            throw new \Exception("回复设置{$type}数据内容无效，缺少media_id，内容:{$content}");
        }
        
        $mediaId = $materialContent->media_id;
        $row = $this->_bizRepository->findMaterial($mediaId, $type);
        if ($row === false) {
            throw new \Exception("回复设置使用的素材已从{$type}素材库删除，素材media_id:{$mediaId}");
        }
        
        return $row;
    }
    
    /**
     * 转换内容格式，由于数据库中存储的格式，不是框架要求的结构，这里做格式上的转换
     * 具体转换后的格式要求 见：app\framework\weixin\msg\BaseHandler\reply方法
     * @param type $type
     * @param type $content
     * @param type $id
     * @return type
     */
    private function convertReplyContentFormat($type, $content, $id = null)
    {
        switch ($type) {
            case '文字':
                // 转换换行符
                return str_replace('<br>', chr(10), $content);
            case '图片':
            case '语音':
                $row = $this->isMaterialContent($type, $content);
                $mediaId = $row['media_id'];
                return $mediaId;
            case '视频':
                $row = $this->isMaterialContent($type, $content);
                return ['MediaId'=>$row['media_id'],'Title'=>$row['title'],'Description'=>$row['summary']];
            case '图文':
                // 图文直接使用存储的内容处理
                $mpnews = json_decode($content);
                if (is_null($mpnews)) {
                    throw new \Exception("回复设置的图文数据内容不是有效的json格式，内容:{$content}");
                }
                $articls = [];
                foreach ($mpnews->articles as $a) {
                    $articls[] = [
                        'Title' => $a->title
                       ,'Description' => $a->summary
                       ,'PicUrl' => $a->cover_url
                       ,'Url' => $a->original_url
                    ];
                }
                
                return $articls;
            case '音乐':
            default :
                throw new \yii\base\NotSupportedException("暂不支持的内容类型[{$type}]");
        }
    }
    
    /**
     * 获取关注欢迎回复，无设置返回false
     * @return mixed
     */
    private function getWelcomeReplyContent()
    {
        // 1、关注->2、回复（非关键字直接回复；关键字匹配关键字）
        $config = $this->_bizRepository->findWelcomeConfig($this->_accountId);
        if ($config === false) {
            return false;
        }
        
        if ($config['type'] == '关键字') {
            return $this->getKeywordReplyContent($config['content'], false);
        }
        
        return ['content-type'=>$config['type'], 'content-data'=>$config['content'], 'id'=>$config['id']];
    }
    
    public static function getAutoReplaySettingCacheKey($wechat)
    {
        return "weixin_reply_autoreply_{$wechat}";
    }

    /**
     * 获取用户输入后的自动回复
     * @return mixed
     */
    public function getAutoReplyContent()
    {
        $cache = \Yii::$app->cache;
        $cacheKey = static::getAutoReplaySettingCacheKey($this->_wechat);
        if (!empty($cacheKey) && $cache->exists($cacheKey)) {
            $replyContent = $cache->get($cacheKey);
            return $replyContent;
        }
        
        $config = $this->_bizRepository->findAutoReplyConfig($this->_accountId);
        if ($config === false || empty($config['content'])) {
            return false;
        }

        if ($config['type'] == '关键字') {
            return $this->getKeywordReplyContent($config['content'], false);
        }
        $config['content'] = $this->convertReplyContentFormat($config['type'], $config['content'], $config['id']);
        $content = ['content-type'=>$config['type'], 'content-data'=>$config['content'], 'id'=>$config['id']];
        $cache->set($cacheKey, $content, $this->_cacheExpireTime);
        return $content;
    }
    
    /**
     * 获取关键字回复，无设置返回false
     * @param string $keyword
     * @param bool $notMatchAutoReply 匹配不到关键字是否自动回复
     * @return mixed
     */
    private function getKeywordReplyContent($keyword, $notMatchAutoReply = true)
    {
        // 1、关键字->2、回复（关键字匹配关键字，不匹配根据参数指定自动回复）
        // 查找关键字
        $config = $this->_bizRepository->findKeywordSetting($this->_accountId, $keyword);
        if ($config !== false) {
            return ['content-type'=>$config['type'], 'content-data'=>$config['content'], 'id'=>$config['id']];
        }

        if ($notMatchAutoReply) {
            return $this->getAutoReplyContent();
        }

        return false;
    }
    
    /**
     * 点击菜单按钮
     * @param string $eventKey
     * @return mixed
     */
    private function getMenuReplayContent($eventKey)
    {
        // 1、菜单事件->2、回复（匹配）
        $config = $this->_bizRepository->findMemuConfig($this->_accountId, $eventKey);
        if ($config === false) {
            return false;
        }
        
        return ['content-type'=>$config['type'], 'content-data'=>$config['content']];
    }
    
    /**
     * 根据群发结果更新群发日志数据
     * @param string $msgId 群发消息id
     * @param string $successful 枚举：发送成功，发送失败
     * @param int $totalCount 发送的总人数
     * @param int $filterCount 准备发送的粉丝数 过滤人数 FilterCount = SentCount + ErrorCount即实际能够接收的人数
     * @param int $sentCount 成功人数
     * @param int $errorCount 失败人数
     * @throws \Exception
     */
    public function updateMassMsgLog($msgId, $successful, $totalCount, $filterCount, $sentCount, $errorCount)
    {
        $affected = $this->_bizRepository->updateMassMsgLog($msgId, $successful, $totalCount, $filterCount, $sentCount, $errorCount);
        if ($affected == 0) {
            \Yii::warning("未更新到群发消息数据，msgId:{$msgId}");
        }
    }
    
    
    /**
     * 更新模板消息发送状态
     * @param string $msgId 消息id
     * @param string $status 枚举：'发送成功','用户拒收失败','非用户拒收失败'
     * @throws \Exception
     */
    public function updateTemplateMsgLog($msgId, $status)
    {
        $affected = $this->_bizRepository->updateTemplateMsgLog($msgId, $status);
        if ($affected == 0) {
            \Yii::warning("未更新到模板消息数据，msgId:{$msgId}");
        }
    }

    public function sendMsg($openid, $text)
    {
        try {
            $customerMsg = new CustomerMessage($this->_accessTokenHelper);
            $customerMsg->sendText($openid, $text);
        } catch (\Exception $ex) {
            \Yii::error($ex->getMessage());
        }
    }
}
