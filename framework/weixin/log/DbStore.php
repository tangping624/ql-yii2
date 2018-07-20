<?php

namespace app\framework\weixin\log;

use app\framework\db\SqlHelper;
use app\framework\db\EntityBase;
class DbStore implements StoreInterface
{

      /**
     * @param string $fromUserName 发送方帐号（一个OpenID）
     * @param string $toUserName 开发者微信号
     * @param string $calledUrl called by weixin
     * @param datetime $receiveTime 接收到事件时间
     * @param int $msg_timestamp 消息创建时间
     * @param string $msgType 消息类型，event
     * @param string $original_xml 微信传入的完整xml内容
     */
    public function insert($fromUserName, $toUserName, $calledUrl, $receiveTime, $msg_timestamp, $msgType, $original_xml)
    {
        $row = [
            'from_user_name' => $fromUserName,
            'to_user_name' => $toUserName,
            'called_url' => $calledUrl,
            'receive_time' => $receiveTime,
            'msg_timestamp' => $msg_timestamp,
            'msg_type' => $msgType,
            'original_xml' => $original_xml
        ];

        
        $conn = EntityBase::getDb();
        SqlHelper::insert('p_msg', $conn, $row, false);
    }
      
}