<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\api\services;

use app\framework\weixin\msg\BaseEventHandler;
use app\modules\api\repositories\BizRepository;
use app\modules\api\services\BizService;
use app\modules\api\services\WeixinHandlerHelper;
/**
 * 微信事件接收处理Handler
 *
 * @author Chenxy
 */
class WeixinEventHandler extends BaseEventHandler
{
    public $_wechat;
    public $_dbConnect;
    public $_bizService; 
    private $_handlerHelper; 
    
    public function __construct($userData = array()) {
        $this->_handlerHelper = new WeixinHandlerHelper();
        parent::__construct($userData);
    }
    
    public static function getCacheKey($wechat, $handlerName, $key = '')
    {
        return "weixin_reply_{$handlerName}_{$wechat}" . empty($key) ? "" : "_{$key}";
    }

    public function beforeHandle($data) {
        $data = parent::beforeHandle($data);
        $this->_handlerHelper->initInternalProperties($this, $data);
        return $data;
    }

    /**
     * 关注
     * @param type $data 参见基类说明
     * @return string
     */
    public function subscribe($data)
    {
        // $cacheKey =''; 表示不缓存
        $cacheKey = static::getCacheKey($this->_wechat, 'subscribe');
        // 粉丝关注+
        $openId = $data['FromUserName'];
        $this->_bizService->addFans($openId);
        
        // 处理消息队列中的关注待发消息
        $this->_bizService->sendInformMsg($openId);
        
        // 关注回复
        $replyContent = $this->_bizService->getReplyContent('关注', $cacheKey);
        // 无设置直接返回
        if ($replyContent === false) {
            return '';
        }
        
        return $this->reply($replyContent['content-type'], $replyContent['content-data']);
    }
   
    /**
     * 取消关注
     * @param type $data 参见基类说明
     */
    public function unsubscribe($data) 
    {
        // 粉丝关注-
        $openId = $data['FromUserName'];
        $unfollowTime = date("Y-m-d H:i:s", $data['CreateTime']);
        $this->_bizService->updateFansUnfollowTime($unfollowTime, $openId);
        return '';
    }
    
    /**
     * 自定义菜单事件
     * @param type $data
     * @return string
     */
    public function click($data)
    { 
        // $cacheKey =''; 表示不缓存
        $cacheKey = static::getCacheKey($this->_wechat, 'click', $data['EventKey']);
        $replyContent = $this->_bizService->getReplyContent('菜单', $cacheKey, $data['EventKey']);
        // 无设置直接返回
        if ($replyContent === false) {
            return '';
        } 
        return $this->reply($replyContent['content-type'], $replyContent['content-data']);
    }
    
    /**
     * 群发消息即将完成时的推送事件
     * @param type $data
     * @return string
     */
    public function masssendjobfinish($data)
    {
        $msgId = $data['MsgID'];
        $successful = strpos(strtolower($data['Status']), 'success') === false ? '发送失败' : '发送成功';
        $totalCount = $data['TotalCount'];
        $filterCount = $data['FilterCount'];
        $sentCount = $data['SentCount'];
        $errorCount = $data['ErrorCount'];
        $this->_bizService->updateMassMsgLog($msgId, $successful, $totalCount, $filterCount, $sentCount, $errorCount);
        return '';
    }
    
    /**
     * 模版消息发送任务完成后的推送事件
     * @param type $data
     */
    public function templatesendjobfinish($data)
    {
        $msgId = $data['MsgID'];
        $status = strtolower(str_replace(" ", "", $data['Status']));
        if (strpos($status, 'failed:userblock') !== false) {
            $status = '用户拒收失败';
        } elseif (strpos($status, 'failed') !== false) {
            $status = '非用户拒收失败';
        } else {
            $status = '发送成功';
        }
        
        $this->_bizService->updateTemplateMsgLog($msgId, $status);
        return '';
    }
}
