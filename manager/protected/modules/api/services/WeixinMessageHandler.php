<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\api\services;

use app\framework\weixin\msg\BaseMessageHandler;
use app\modules\api\repositories\BizRepository;
use app\modules\api\services\BizService;
use app\modules\api\services\WeixinHandlerHelper;

/**
 * 微信用户消息类处理Handler
 *
 * @author Chenxy
 */
class WeixinMessageHandler extends BaseMessageHandler {

    public $_wechat;
    public $_dbConnect;
    public $_bizService;
    private $_handlerHelper; 

    public function __construct($userData = []) {
        $this->_handlerHelper = new WeixinHandlerHelper();
        parent::__construct($userData);
    }

    public static function getCacheKey($wechat, $handlerName, $key = '') {
        return "weixin_reply_{$handlerName}_{$wechat}" . empty($key) ? "" : "_{$key}";
    }

    public function beforeHandle($data) {
        $data = parent::beforeHandle($data);
        $this->_handlerHelper->initInternalProperties($this, $data);
        return $data;
    }

    /**
     * 用户向公众号发送图片
     * @param type $data
     * @return string
     */
    public function image($data) {
        return '';
    }

    /**
     * 语音消息：用户发送一段语音
     * @param array $data 事件接收的xml数据的数组格式['ToUserName'=>开发者微信号,'FromUserName'=>OpenID,'CreateTime'=>消息创建时间戳,'MsgType'=>'voice','Format'=>'语音格式，如amr','MediaId'=>语音消息媒体id, 'MsgId'=>消息id] + userData
     * @return string
     */
    public function voice($data) {
        return '';
    }

    /**
     * 视频消息:用户发送一段视频
     * @param array $data 事件接收的xml数据的数组格式['ToUserName'=>开发者微信号,'FromUserName'=>OpenID,'CreateTime'=>消息创建时间戳,'MsgType'=>'video','ThumbMediaId'=>'视频消息缩略图的媒体id','MediaId'=>语音消息媒体id, 'MsgId'=>消息id] + userData
     * @return string
     */
    public function video($data) {

        return '';
    }

    /**
     * 小视频消息:用户录制发送一段小视频
     * @param array $data 事件接收的xml数据的数组格式['ToUserName'=>开发者微信号,'FromUserName'=>OpenID,'CreateTime'=>消息创建时间戳,'MsgType'=>'shortvideo','ThumbMediaId'=>'视频消息缩略图的媒体id','MediaId'=>语音消息媒体id, 'MsgId'=>消息id] + userData
     * @return string
     */
    public function shortvideo($data) {

        return '';
    }

    /**
     * 位置消息:用户发送一个位置
     * @param array $data 事件接收的xml数据的数组格式['ToUserName'=>开发者微信号,'FromUserName'=>OpenID,'CreateTime'=>消息创建时间戳,'MsgType'=>'location','Location_X'=>'纬度','Location_Y'=>经度, 'Scale'=>'缩放大小','Label'=>地理位置信息,'MsgId'=>消息id] + userData
     * @return string
     */
    public function location($data) {

        return '';
    }

    /**
     * 链接消息：用户发送一个链接
     * @param array $data 事件接收的xml数据的数组格式['ToUserName'=>开发者微信号,'FromUserName'=>OpenID,'CreateTime'=>消息创建时间戳,'MsgType'=>'link','Title'=>'消息标题','Description'=>消息描述, 'Url'=>'消息链接','MsgId'=>消息id] + userData
     * @return string
     */
    public function link($data) {

        return '';
    }

    /**
     * 自动回复，场景：用户输入文字
     * @param type $data 参见基类说明
     */
    public function text($data) {
        // 表示不缓存，关键字不能缓存
        try {
            $cacheKey = '';
            $replyContent = $this->_bizService->getReplyContent('关键字', $cacheKey, $data['Content']);
            if ($replyContent !== false && $replyContent['content-type'] == '图文') {
                $articls = $replyContent['content-data'];
                $newArticls = [];
                foreach ($articls as $a) {
                    $newArticls[] = [
                        'Title' => $a['Title']
                        , 'Description' => $a['Description']
                        , 'PicUrl' => $a['PicUrl']
                        , 'Url' => $a['Url'] . (strpos($a['Url'], '?') === false ? '?' : '&') . "openid=" . $data['FromUserName']
                    ];
                }
                $replyContent['content-data'] = $newArticls;
            }
        } catch (\Exception $ex) {
            \Yii::error($ex->getMessage());
            throw $ex;
        }

        // 无设置直接返回
        if ($replyContent === false) {

            try {
                //如果没有多客服，则走自动回复 
                $replyContent = $this->_bizService->getAutoReplyContent();
                if ($replyContent === false) {
                    return '';
                }
            } catch (\Exception $ex) {
                \Yii::error($ex->getMessage());
                throw $ex;
            }
        }
        return $this->reply($replyContent['content-type'], $replyContent['content-data']);
    }

}
