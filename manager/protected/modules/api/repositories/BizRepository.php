<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\api\repositories;

use yii\db\Query;

/**
 * Description of BizRepository
 *
 * @author Chenxy
 */
class BizRepository
{
    private $_dbConnection;
    public function __construct($dbConnetion)
    {
        $this->_dbConnection = $dbConnetion;
    }


    public function addFans($columnsData)
    {
        $columnsData['id'] = \app\framework\utils\StringHelper::uuid();
        $columnsData['month_pushed'] = 0;
        $columnsData['is_deleted'] = 0;
        $columnsData['is_followed'] = 1;
        
        $this->_dbConnection->createCommand()->insert('p_fan', $columnsData)->execute();
        return $columnsData;
    }
    
    public function updateFansInfo($columnsData, $accountId, $openId)
    {
        $columnsData['modified_on'] = \app\framework\utils\DateTimeHelper::now();
        //return $this->_dbConnection->createCommand()->update('p_fan', $columnsData, "openid='{$openId}' and account_id='{$accountId}'")->execute();
        return $this->_dbConnection->createCommand()->update('p_fan', $columnsData, ['openid'=>$openId, 'account_id'=>$accountId])->execute();
    }
    
    public function findFansBy($accountId, $openId, $columns)
    {
        $sql = "select {$columns} from p_fan where openid=:openid and account_id=:account_id and is_deleted=0";
        $data = $this->_dbConnection->createCommand($sql, [':openid'=>$openId, ':account_id'=>$accountId])->queryOne();
        return $data;
    }
    
    public function findWelcomeConfig($accountId)
    {
        $data = $this->_dbConnection->createCommand(
            "select type,content,id from p_welcome where account_id=:account_id and is_deleted=0",
            [':account_id'=>$accountId]
        )->queryOne();
        
        return $data;
    }
    
    public function findAutoReplyConfig($accountId)
    {
        $data = $this->_dbConnection->createCommand(
            "select type,content,id from p_reply where account_id=:account_id and is_deleted=0",
            [':account_id' => $accountId]
        )->queryOne();
        
        return $data;
    }
    
    public function findKeywordSetting($accountId, $keyword)
    {
        // 完全匹配
        $setId = $this->_dbConnection->createCommand(
            "select set_id from p_keyword where is_deleted = 0 and account_id=:account_id and keyword =:keyword and is_exact=1",
            [':account_id' => $accountId, ':keyword' => $keyword]
        )->queryScalar();
        
        if ($setId === false) {
            // 模糊匹配
//            $setId = $this->_dbConnection->createCommand(
//                    "select set_id from p_keyword where is_deleted = 0 and account_id=:account_id and keyword like '%{$keyword}%'  and is_exact=0"
//                    ,[':account_id'=>$accountId])->queryScalar();
            $query = new Query();
            $setId = $query->from('p_keyword')->where(['is_deleted'=>0, 'account_id'=>$accountId, 'is_exact'=>0])
                ->andWhere(['like', 'keyword', $keyword])
                ->select('set_id')
                ->createCommand($this->_dbConnection)
                ->queryScalar();
        }
        
        // 没找到数据
        if ($setId === false) {
            return false;
        }
        
        // 查找配置
        $data = $this->_dbConnection->createCommand(
            "select type,content,id from p_keyword_set where id=:id and is_deleted=0",
            [':id' => $setId]
        )->queryOne();

        return $data;
    }
    
    public function findMemuConfig($accountId, $eventKey)
    {
        $data = $this->_dbConnection->createCommand(
            "select type,content from p_menu where account_id=:account_id and is_deleted=0 and id=:event_key",
            [':account_id' => $accountId, ':event_key'=> $eventKey]
        )->queryOne();
        
        return $data;
    }
    
    /**
     * 更新群发消息报名
     * @param string $msgId 消息ID
     * @param string $successful 枚举：发送成功，发送失败
     * @param int $totalCount 发送的总人数
     * @param int $filterCount 过滤人数 = 成功人数+失败人数
     * @param int $sentCount 成功人数
     * @param int $errorCount 失败人数
     * @return int integer number of rows affected by the execution.
     */
    public function updateMassMsgLog($msgId, $successful, $totalCount, $filterCount, $sentCount, $errorCount)
    {
        // 不使用事务，反应实际发送情况
        $affected = $this->_dbConnection->createCommand()->update(
            'p_mass_msg_batch',
            ['status' => $successful
                ,'total_count' => $totalCount
                ,'filter_count' => $filterCount
                ,'sent_count' => $sentCount
                ,'error_count' => $errorCount
                ,'modified_on' => date('Y-m-d H:i:s')],
            ['msg_id' => $msgId]
        )->execute();
        
        $massMsgId = $this->_dbConnection->createCommand(
            "select mass_msg_id from p_mass_msg_batch where msg_id=:msg_id",
            [':msg_id' => $msgId]
        )->queryScalar();
        
        $arrStatus = $this->_dbConnection->createCommand(
            "select status from p_mass_msg_batch where mass_msg_id=:mass_msg_id",
            [':mass_msg_id' => $massMsgId]
        )->queryAll();
        
        // 采用重新汇总total_count, filter_count、sent_count、error_count（原因：微信可能出现同一群发重新推的情况）
        $massSummary = $this->_dbConnection->createCommand(
            "select ifnull(sum(total_count),0) as total_count, ifnull(sum(filter_count),0) as filter_count,ifnull(sum(sent_count),0) as sent_count,ifnull(sum(error_count),0) as error_count from p_mass_msg_batch where mass_msg_id=:id",
            [':id'=>$massMsgId]
        )->queryOne();
                
        $updateData = ['filter_count' => $massSummary['filter_count']
                ,'sent_count' => $massSummary['sent_count']
                ,'error_count' => $massSummary['error_count']
                ,'modified_on' => date('Y-m-d H:i:s')
                ,'total_count' => $massSummary['total_count']
            ];
        
        $allSuccessful = true;
        foreach ($arrStatus as $s) {
            if ($s['status'] != '发送成功') {
                $allSuccessful = false;
                break;
            }
        }
        
        if ($allSuccessful) {
            $updateData['status'] = '发送成功';
        }
        
        if ($successful == '发送失败') {
            $updateData['status'] = $successful;
        }
        
        $this->_dbConnection->createCommand()->update(
            'p_mass_msg',
            $updateData,
            ['id' => $massMsgId]
        )->execute();
        
                
        return $affected;
    }
    
    /**
     * 更新模板消息状态
     * @param string $msgId 消息id
     * @param string $status 枚举：'发送成功','用户拒收失败','非用户拒收失败'
     * @return int integer number of rows affected by the execution.
     */
    public function updateTemplateMsgLog($msgId, $status)
    {
        $affected = $this->_dbConnection->createCommand()->update(
            'p_template_msg',
            ['status' => $status],
            ['msg_id' => $msgId]
        )->execute();
                
        return $affected;
    }
    
    /**
     * 根据media_id查找永久素材
     * @param guid $mediaId
     * @param string $materialType 枚举：图片、语音、视频、图文
     * @return type
     * @throws \InvalidArgumentException
     */
    public function findMaterial($mediaId, $materialType)
    {
        switch (strtolower($materialType)) {
            case '图片':
                $sql = "select id,name,img_url,wechat_url,modified_on,media_id from p_picture where media_id=:media_id and is_deleted=0";
                break;
            case '语音':
                $sql = "select id,name,voice_url,modified_on,media_id from p_voice where media_id=:media_id and is_deleted=0";
                break;
            case '视频':
                $sql = "select id,ifnull(title,'') as title,ifnull(summary,'') as summary,type,video_url,modified_on,media_id from p_video where media_id=:media_id and is_deleted=0";
                break;
            case '图文':
                 $sql = "select media_id,account_id, id, modified_on from p_mpnews where media_id=:media_id and is_deleted=0";
                break;
            default :
                throw new \InvalidArgumentException("参数materialType无效，允许的值：图片、语音、视频、图文,当前值：{$materialType}");
        }
        
        $row = $this->_dbConnection->createCommand(
            $sql,
            [':media_id'=>$mediaId]
        )->queryOne();
        
        return $row;
    }
}
