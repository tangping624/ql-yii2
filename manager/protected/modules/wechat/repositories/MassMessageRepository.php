<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\wechat\repositories;

use app\modules\RepositoryBase;
use app\framework\utils\StringHelper;
use app\entities\EntityBase;
use app\framework\utils\DateTimeHelper;
use yii\db\Query;
use app\framework\db\SqlHelper;

/**
 * Description of MassMessageRepository
 *
 * @author Chenxy
 */
class MassMessageRepository extends RepositoryBase
{
    // 集团id
    const GROUP_ID_STRING = SUPER_ORGANIZATION_ID;

    /**
     * 获取指定公众号下指定公司区域的所有已关注的认证业主
     * @param string $accountId
     * @param string $corpIds 11b11db4-e907-4f1f-8835-b9daab6e1f23表示集团下粉丝
     * @param string $buildingIds
     * @return array [['id'=>粉丝id, 'openid'=>],.....]
     */
    public function getRoomOwners($accountId, $corpIds, $buildingIds)
    {
        $conn = EntityBase::getDb();
        $query = new Query();
        
        $query->from('p_fan f')
            ->where(['f.account_id'=>$accountId, 'f.is_deleted'=>0, 'f.is_followed'=>1])
            ->select('f.openid, f.id, f.member_id')
            ->distinct()
            ->innerJoin('h_member m', "m.id=f.member_id and m.type='业主' and m.is_authenticated=1 and m.is_deleted=0")
            ->innerJoin('h_member_room r', 'm.id=r.member_id and r.is_deleted=0');
        
        // 公司过滤
        if (strtolower($corpIds) != static::GROUP_ID_STRING) {
            //会籍或者房产属于该公司的业主
            $query->andWhere(['or',['IN', 'm.corp_id', explode(",", $corpIds)]
                ,['IN', 'r.corp_id', explode(",", $corpIds)]
            ]);
        }
        
        // 楼栋过滤
        if (!empty($buildingIds)) {
            $query->andWhere(['in', 'r.building_id', explode(",", $buildingIds)]);
        }
        
        $rows = $query->createCommand($conn)->queryAll();
        
        return $rows;
    }
    
    /**
     * 获取指定公众号下指定公司区域的所有已关注的会员
     * @param guid $accountId 
     * @param string $levelIds
     * @return array [['id'=>粉丝id, 'openid'=>],.....]
     */
    public function getMembers($accountId, $levelIds = '')
    {
        $conn = EntityBase::getDb();
        $query = new Query();
        
        $query->from('p_fan f')->where(['f.account_id'=>$accountId, 'f.is_deleted'=>0, 'f.is_followed'=>1])
            ->select('f.openid, f.id, f.member_id')
            ->distinct()
            ->innerJoin('h_member m', "m.id=f.member_id and m.is_deleted=0"); 
        // 会员等级过滤
        if (!empty($levelIds)) {
            $query->andWhere(['IN', 'm.level_id', explode(",", $levelIds)]);
        } 
        $rows = $query->createCommand($conn)->queryAll(); 
        return $rows;
    }
     


    /**
     * 获取指定公众号下指定公司区域的所有已关注的粉丝
     * @param string $accountId 
     * @return array  [['id'=>粉丝id, 'openid'=>],.....]
     */
    public function getFans($accountId)
    {
 
        $conn = EntityBase::getDb();
        $query = new Query();
        $query->from('p_fan')->where(['is_deleted'=>0, 'account_id'=>$accountId, 'is_followed'=>1])->select('openid, id, member_id'); 
        $rows = $query->createCommand($conn)->queryAll(); 
        return $rows;
    }
    
    public function getFanMassMsgPushByInIds($arrayFanIds)
    {
        // 采用SQL拼接，避免arrayFanIds数组过大造成的性能问题，这里结合上下文不会产生SQL注入的可能
        $inWhere = implode("','", $arrayFanIds);
        $sql = "select id, month_pushed, month_pushed_modified
                ,if(month_pushed < 4 or date_format(now(),'%Y-%m') <> date_format(month_pushed_modified,'%Y-%m'),0,1) as isOutFourTimes
                from p_fan 
                where id in ('{$inWhere}')";
        $conn = EntityBase::getDb();
        $rows = $conn->createCommand($sql)
                ->queryAll();
        
        return $rows; 
    }

    public function insertBatchMassMsg($massMsgId, $msgMassRowData, $msgMassBatchRowData, $massMsgObjectRows, $fanMassMsgPushRows)
    {
        $now = DateTimeHelper::now();
        $conn = EntityBase::getDb();
        $trans = $conn->beginTransaction();
        try {
            if (!empty($msgMassRowData)) {
                $conn->createCommand()->update('p_mass_msg', $msgMassRowData, ['id' => $massMsgId])->execute();
            }
            
            $affected = $conn->createCommand()->update('p_mass_msg_batch', $msgMassBatchRowData, ['id' => $msgMassBatchRowData['id']])->execute();
            /* 避免大数量时超时的问题暂时不做发送对象明细及月次数处理
            $cols = ['fan_id','member_id','id','mass_msg_id','batch_id','has_send','is_deleted','created_by','modified_by'];
            $conn->createCommand()->batchInsert('p_mass_msg_object', $cols, $massMsgObjectRows)->execute();
               
            // 更新粉丝的发送情况
            foreach ($fanMassMsgPushRows as $key => $value) {
                $updateValue['month_pushed'] = $value['isOutFourTimes'] ? 1 : ($value['month_pushed'] + 1);
                $updateValue['month_pushed_modified'] = $now;
                $conn->createCommand()->update('p_fan', $updateValue, ['id' => $key])->execute();
            }*/
        } catch (\Exception $ex) {
            $trans->rollback();
            throw $ex;
        }
        
        $trans->commit();
        return isset($affected) ? $affected : 0;
    }
    
    public function insertMassMsg($rowData)
    {
        $conn = EntityBase::getDb();
        return $conn->createCommand()->insert('p_mass_msg', $rowData)->execute();
    }

    public function insertMassMsgBatch($rowData)
    {
        $conn = EntityBase::getDb();
        return $conn->createCommand()->insert('p_mass_msg_batch', $rowData)->execute();
    }

    public function deleteMassMsgBatch($batchId)
    {
        $conn = EntityBase::getDb();
        return $conn->createCommand()->delete('p_mass_msg_batch', [ 'id' => $batchId])->execute();
    }

    public function getMassMsgBatch($mediaId, $openIds)
    {
        $query = (new \yii\db\Query())
            ->select('p_mass_msg_batch.id')
            ->from('p_mass_msg')
            ->innerJoin('p_mass_msg_batch', 'p_mass_msg.id = p_mass_msg_batch.mass_msg_id')
            ->where(['=', 'p_mass_msg.media_id', $mediaId])
            ->andWhere(['=', 'p_mass_msg_batch.openids', $openIds]);

        $connection = EntityBase::getDb();
        $command = $query->createCommand($connection);
        $rows = $command->queryOne();
        return $rows;
    }

    /**
     * 物理删除群发记录
     * @param type $massMsgId
     * @return type
     */
    public function deleteMassMsg($massMsgId)
    {
        $conn = EntityBase::getDb();
        return $conn->createCommand()->delete('p_mass_msg', ['id' => $massMsgId])->execute();
    }

    /**
     * 获取所有群发消息日志
     * @param string $accountId 公众号id
     * @return array ['total' => $count, 'data' => $rows]
     */
    public function getMassMsgLog($accountId, $offset, $limit)
    {
        $sql = "select id, account_id, object_type
                ,mpnews_cover_url, mpnews_title, mpnews_summary
                ,msg_type,media_id,text_msg,title
                ,description,card_id,status,send_time
                ,total_count,filter_count,sent_count,error_count
                from p_mass_msg 
                where account_id=:account_id and status in ('发送中','发送成功','发送失败','已撤销')
                order by send_time desc
                limit {$offset}, {$limit}";
        $conn = EntityBase::getDb();
        $rows = $conn->createCommand($sql, [':account_id'=>$accountId])
                ->queryAll();
        
        $count = $conn->createCommand(
            "select count(*)
            from p_mass_msg 
            where account_id=:account_id and status in ('发送中','发送成功','发送失败','已撤销') and is_deleted=0",
            [':account_id'=>$accountId]
        )->queryScalar();
        
        return ['total' => $count, 'data' => $rows];
    }
    
    /**
     * 获取群发审批人，成功返回审批人member_id失败返回''
     * @param type $accountId
     * @return type
     */
    public function getMassMsgApprover($accountId)
    {
        $sql = "select member_id
                from p_mass_msg_approver 
                where account_id=:account_id and is_deleted=0";
        $conn = EntityBase::getDb();
        $approver = $conn->createCommand($sql, [':account_id'=>$accountId])
                ->queryScalar();  
        return $approver;
    }

     
    
    /**
     * 根据手机号获取一个关注者的粉丝信息
     * @param type $accountId
     * @param type $mobile
     * @return type
     */
    public function getFollowedFanInfo($accountId, $mobile)
    {
        $sql = "select f.openid, f.nick_name, m.name as member_name, f.account_id
                from h_member as m
                inner join p_fan as f on m.id = f.member_id
                where m.mobile=:mobile and f.is_deleted=0 and f.is_followed=1 and f.account_id=:account_id
                ";
        $conn = EntityBase::getDb();
        $row = $conn->createCommand($sql, [':mobile'=>$mobile, ':account_id'=>$accountId])
                ->queryOne();
        
        return $row;
    }

    public function savePreviewHistory($userId, $fanInfo)
    {
        if (!$fanInfo) {
            return false;
        }
        $conn = EntityBase::getDb();
        $id = (new Query())
            ->select('id')
            ->from('p_mass_preview')
            ->where('account_id=:account_id and openid=:openid and created_by=:created_by and is_deleted=0', [':account_id'=>$fanInfo['account_id'], ':openid'=>$fanInfo['openid'], ':created_by'=>$userId])
            ->createCommand($conn)
            ->queryScalar();

        $now = date('Y-m-d H:i:s');
        if ($id) {
            //更新
            $updateData = [
                'mobile'=>$fanInfo['mobile'],
                'name'=>$fanInfo['member_name'] ? $fanInfo['member_name'] : $fanInfo['nick_name'],
                'mpnews_id'=>$fanInfo['media_id'],
                'preview_time'=>$now,
            ];
            SqlHelper::update('p_mass_preview', $conn, $updateData, 'id=:id', [':id'=>$id]);
        } else {
            $insertData = [
                'id'=>StringHelper::uuid(),
                'account_id'=>$fanInfo['account_id'],
                'mobile'=>$fanInfo['mobile'],
                'name'=>$fanInfo['member_name'] ? $fanInfo['member_name'] : $fanInfo['nick_name'],
                'openid'=>$fanInfo['openid'],
                'mpnews_id'=>$fanInfo['media_id'],
                'preview_time'=>$now
            ];
            SqlHelper::insert('p_mass_preview', $conn, $insertData);
        }

        return true;
    }

    public function getPreviewHistory($userId,$accountId, $cnt = 12)
    {
        $conn = EntityBase::getDb();

        return (new Query())
            ->select('m.mobile,m.name,f.nick_name')
            ->from('p_mass_preview p')
            ->innerJoin('p_fan f', 'f.openid=p.openid')
            ->innerJoin('h_member m', 'f.member_id=m.id and m.account_id=:accountid')
            ->where('p.created_by=:created_by and p.is_deleted=0 and m.is_deleted=0 and f.is_deleted=0 and f.is_followed=1', [':created_by'=>$userId,':accountid'=>$accountId])
            ->orderBy('p.preview_time desc')
            ->limit($cnt)
            ->createCommand($conn)
            ->queryAll();
    }
    
    /**
     * 撤消已发送的消息
     * @param guid $massMsgId
     * @param guid $modifiedBy 操作人
     * @return int
     */
    public function cancelMassMsg($massMsgId, $modifiedBy)
    {
        $conn = EntityBase::getDb();
        $trans = $conn->beginTransaction();
        try {
            $affected = $conn->createCommand()->update(
                'p_mass_msg',
                ['status' => '已撤销'
                ,'modified_on' => DateTimeHelper::now()
                ,'modified_by' => $modifiedBy],
                ['id' => $massMsgId]
            )
            ->execute();
            
            $conn->createCommand()->update(
                'p_mass_msg_batch',
                ['status' => '已撤销'
                ,'modified_on' => DateTimeHelper::now()
                ,'modified_by' => $modifiedBy],
                ['mass_msg_id' => $massMsgId]
            )
            ->execute();
            
        } catch (Exception $ex) {
            $trans->rollback();
            throw $ex;
        }
        
        
        $trans->commit();
        return $affected;
    }
    
    public function getMsgIdByMassId($massMsgId)
    {
        $query = new Query();
        $rows = $query->from('p_mass_msg_batch')
              ->where("mass_msg_id=:mass_msg_id and is_deleted=0", [':mass_msg_id' => $massMsgId])
              ->select("msg_id")
              ->createCommand(EntityBase::getDb())
              ->queryAll();
        return $rows;
    }

    /**
     * 查找群发日志记录
     * @param string $massMsgId pk
     * @param string|array $fields
     * @return array row
     */
    public function findMassMsg($massMsgId, $fields = '*')
    {
        if (empty($fields)) {
            throw new \InvalidArgumentException('$fields');
        }

        $query = new Query();
        $row = $query->from('p_mass_msg')
              ->where("id=:id and is_deleted=0", [':id' => $massMsgId])
              ->select($fields)
              ->createCommand(EntityBase::getDb())
              ->queryOne();
        return $row;
    }

    
    public function updateMassMsgStatus($status, $massMsgId)
    {
        try {
            $dbConn = EntityBase::getDb();
            return $dbConn->createCommand()->update('p_mass_msg', ['status'=>$status, 'modified_on' => DateTimeHelper::now()], ['id' => $massMsgId])->execute();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function updateMassMsgStatusByPreviousStatus($status, $massMsgId, $previousStatus)
    {
        try {
            $dbConn = EntityBase::getDb();
            return $dbConn->createCommand()->update('p_mass_msg', ['status'=>$status, 'modified_on' => DateTimeHelper::now()], ['and', ['=','id',$massMsgId], ['in','status',$previousStatus]])->execute();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 查找公众号
     * @param $accountId
     * @return array row
     */
    public function getAccountByAccountId($accountId)
    {
        $query = new Query();
        $row = $query->from('p_account')
            ->where("id=:id and is_deleted=0", [':id' => $accountId])
            ->select("*")
            ->createCommand(EntityBase::getDb())
            ->queryOne();
        return $row;
    }

    /**
     * 查找粉丝信息
     * @param $accountId
     * @return array row
     */
    public function getFanByFanId($fanId)
    {
        $query = new Query();
        $row = $query->from('p_fan')
            ->where("id=:id and is_deleted=0", [':id' => $fanId])
            ->select("*")
            ->createCommand(EntityBase::getDb())
            ->queryOne();
        return $row;
    }

    /**
     * 获得会员等级列表
     * @param OceanDeng (denghg@mysoft.com.cn)
     * @return array
     */
    public function getMemberLevelList($accountId)
    {
        $query = (new Query())
            ->select('id,name,privilege,img_url,sort,card_no_location,card_no_color')
            ->from('h_member_level')
            ->Where('is_deleted=0 and account_id=:account_id',[':account_id'=>$accountId])
            ->orderBy(['sort' => SORT_ASC]);

        $connection = EntityBase::getDb();
        $command = $query->createCommand($connection);
        $rows = $command->queryAll();
        return $rows;
    }
}
