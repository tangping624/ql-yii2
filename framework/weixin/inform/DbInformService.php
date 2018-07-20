<?php

namespace app\framework\weixin\inform;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 通知消息存储在数据库表p_inform中
 *
 * @author Chenxy
 */
use app\framework\weixin\inform\IInformService;
use app\framework\db\EntityBase;

class DbInformService implements IInformService
{
    private $_dbConnection;
    
    public function __construct() 
    {
       
        $this->_dbConnection = EntityBase::getDb();
    }
    
    /**
     * 插入一条消息入队列
     * @param string $toUser openid
     * @param string $fromUser 公众号id
     * @param string $type 允许的值：'文字','图片','图文','语音','音乐','视频'
     * @param string $content
     * @return int
     */
    public function insert($toUser, $fromUser, $type, $content)
    {
        $arrType = ['文字','图片','图文','语音','音乐','视频'];
        if (!in_array($type, $arrType)) {
            throw new \InvalidArgumentException("参数type值无效，有效的值:" . implode('、', $arrType));
        }
        
        // 去重，如果已存在则不插入
        $isExist = $this->find($fromUser, $toUser, $type, $content);
        if ($isExist) {
            return;
        }
        
        $row = [
            'id' => \app\framework\utils\StringHelper::uuid(),
            'openid' => $toUser,
            'account_id' => $fromUser,
            'type' => $type,
            'content' => $content,
            'created_on' => date('Y-m-d H:i:s'),
            'is_deleted' => 0
        ];

        $this->_dbConnection->createCommand()->insert('p_inform', $row)->execute();
        return $row['id'];
    }
    
    /**
     * 从队列中移除指定的消息
     * @param guid $msgId
     * @return int
     */
    public function remove($msgId)
    {
        return $this->_dbConnection->createCommand()->update('p_inform', ['is_deleted'=>1, 'modified_on' => date('Y-m-d H:i:s')], "id='{$msgId}'")->execute();
    }
    
    /**
     * 搜索队列消息(内容去重)
     * @param type $fromUser
     * @param type $toUser
     * @return type
     */
    public function search($fromUser, $toUser)
    {
        $rows = $this->_dbConnection->createCommand(
                "select id, type,content from p_inform
                 where openid=:openid and account_id=:account_id and is_deleted=0 order by created_on asc",
                [':openid' => $toUser, ':account_id' => $fromUser])
                ->queryAll();
        
        // 按内容去重
        $arrContent = [];
        $resultRows = [];
        foreach ($rows as $r) {
            if (!in_array($r['content'], $arrContent)) {
                $resultRows[] = $r;
                $arrContent[] = $r['content'];
            }
        }
        
        return $resultRows;
    }
    
    private function find($fromUser, $toUser, $type, $content)
    {
        $row = $this->_dbConnection->createCommand(
                "select id, type,content from p_inform
                 where openid=:openid and account_id=:account_id and type=:type and content=:content and is_deleted=0",
                [':openid' => $toUser, ':account_id' => $fromUser, ':type' => $type, ':content' => $content])
                ->queryOne();
        
        return $row;
    } 
}
