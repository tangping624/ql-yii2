<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\wechat\repositories;

use app\framework\biz\cache\SiteCacheManager;
use app\framework\utils\WebUtility;
use app\modules\RepositoryBase;
use app\framework\utils\StringHelper;
use app\entities\EntityBase;
use app\framework\utils\DateTimeHelper;
use yii\db\Query;

/**
 * Description of MaterialRepository
 *
 * @author Chenxy
 */
class MaterialRepository extends RepositoryBase
{
    /**
     * 获取图文列表
     * @param type $accountId
     * @return array [['id'=>],['id'=>],...]
     */
    public function getMpnewsList($accountId, $keyword, $offset, $limit)
    {
        $conn = EntityBase::getDb();
        $inIdFitler = [];
        if (!empty($keyword)) {
            $mpNewsIds = (new Query())
                ->from('p_article')
                ->where(['is_deleted'=>0])
                ->andWhere(['OR', ['like', 'title', $keyword], ['like', 'author', $keyword], ['like', 'summary', $keyword]])
                ->select('mpnews_id')
                ->distinct()
                ->createCommand($conn)
                ->queryAll();

            foreach ($mpNewsIds as $id) {
                $inIdFitler[] = $id['mpnews_id'];
            }
        }
        
        $rowsQuery = (new Query())
            ->from('p_mpnews')
            ->where(['is_deleted'=>0, 'account_id'=>$accountId])
            ->orderBy('modified_on desc')
            ->offset($offset)->limit($limit)
            ->select('id, modified_on, media_id ');

        $countQuery = (new Query())
            ->from('p_mpnews')
            ->where(['is_deleted'=>0, 'account_id'=>$accountId])
            ->select('count(*)');

        $rows = [];
        $count = 0;
        if ($inIdFitler && count($inIdFitler) > 0) {
            $rows = $rowsQuery->andWhere(['IN', 'id', $inIdFitler])->createCommand($conn)->queryAll();
            $count = $countQuery->andWhere(['IN', 'id', $inIdFitler])->createCommand($conn)->queryScalar();
        } elseif (empty($keyword)) {
            $rows = $rowsQuery->createCommand($conn)->queryAll();
            $count = $countQuery->createCommand($conn)->queryScalar();
        }
                
        return ['total' => $count, 'data' => $rows];
    }
    
    /**
     * 获取图文文章列表
     * @param string $mpnewsId
     * @return array
     */
    public function getNewsArticlesBy($mpnewsId)
    {
        $query = new \yii\db\Query();
        $rows = $query->from('p_article')
                ->where(['mpnews_id' => $mpnewsId, 'is_deleted'=>0])
                ->select('id, title, author, cover_url, is_cover_showin_body, summary, body, original_url, share_point')
                ->orderBy("sort asc")
                ->createCommand(EntityBase::getDb())
                ->queryAll();
        
        return $rows;
    }
    
    /**
     * 根据图文的media_id获取第一篇文章
     * @param type $mediaId
     * @return array [title=>标题,author=>作者，cover_url=>封面url,summary=>摘要]
     */
    public function getFirstArticle($mediaId)
    {
        $query = new \yii\db\Query();
        $mpnewsId = $query->from('p_mpnews')
                ->where(['media_id' => $mediaId])
                ->select('id')
                ->createCommand(EntityBase::getDb())
                ->queryScalar();
        
        $row = $query->from('p_article')
                ->where(['mpnews_id' => $mpnewsId, 'is_deleted' => 0])
                ->select('title,author,cover_url,summary')
                ->orderBy("sort ASC")
                ->createCommand(EntityBase::getDb())
                ->queryOne();
        
        return $row;
    }

    /**
     * 获取图片分组
     * @param type $accountId
     * @return type
     */
    public function getPictureGroupList($accountId)
    {
        $conn = EntityBase::getDb();
        $inGroupRows = $conn->createCommand(
            "select a.id, a.name,
            (select count(*) from p_picture where group_id=a.id and is_deleted=0) as total
            from p_picture_group as a 
            where a.account_id=:account_id and a.is_deleted=0 
            order by a.created_on asc",
            [':account_id' => $accountId]
        )->queryAll();

        $notInGroupRows = $conn->createCommand(
            "select '' as id, '未分组' as name, count(*)  as total
            from p_picture 
            where group_id is null and account_id=:account_id and is_deleted=0",
            [':account_id' => $accountId]
        )->queryAll();
        
        $rows = array_merge($notInGroupRows, $inGroupRows);
        
        return $rows;
    }
    
    /**
     * 获取图片列表
     * @param type $accountId
     * @param type $offset
     * @param type $limited
     * @return type
     */
    public function getPictureList($accountId, $groupId, $offset, $limit)
    {
        $conn = EntityBase::getDb();
        $rowsQuery = (new Query())
                    ->from('p_picture')
                    ->where(['account_id'=>$accountId, 'is_deleted'=>0])
                    ->orderBy('modified_on desc')->offset($offset)->limit($limit)
                    ->select('id,name,img_url,wechat_url,modified_on,media_id');

        $countQuery = (new Query())
            ->from('p_picture')
            ->where(['account_id'=>$accountId, 'is_deleted'=>0])
            ->select('count(*)');

        if (empty($groupId)) {
            $rows = $rowsQuery->andWhere(['is', 'group_id', null])->createCommand($conn)->queryAll();
            $count = $countQuery->andWhere(['is', 'group_id', null])->createCommand($conn)->queryScalar();
        } else {
            $rows = $rowsQuery->andWhere(['=', 'group_id', $groupId])->createCommand($conn)->queryAll();
            $count = $countQuery->andWhere(['=', 'group_id', $groupId])->createCommand($conn)->queryScalar();
        }

        return ['total' => $count, 'data' => $rows];
    }
    
    /**
     * 获取音频素材列表
     * @param type $accountId
     * @param type $offset
     * @param type $limit
     * @return type
     */
    public function getVocieList($accountId, $offset, $limit)
    {
        $conn = EntityBase::getDb();
        $rows = $conn->createCommand(
            "select id,name,voice_url,modified_on,media_id
            from p_voice 
            where account_id=:account_id and is_deleted=0 
            order by modified_on desc limit {$offset}, {$limit}",
            [':account_id' => $accountId]
        )->queryAll();
        
        $count = $conn->createCommand(
            "select count(*)
            from p_voice 
            where account_id=:account_id and is_deleted=0",
            [':account_id' => $accountId]
        )->queryScalar();
                
        return ['total' => $count, 'data' => $rows];
    }
    
    /**
     * 获取视频素材列表
     * @param type $accountId
     * @param type $offset
     * @param type $limit
     * @return type
     */
    public function getVideoList($accountId, $offset, $limit)
    {
        $conn = EntityBase::getDb();
        $rows = $conn->createCommand(
            "select id,title,summary,type,video_url,modified_on,media_id
            from p_video 
            where account_id=:account_id and is_deleted=0 
            order by modified_on desc limit {$offset}, {$limit}",
            [':account_id' => $accountId]
        )->queryAll();
        
        $count = $conn->createCommand(
            "select count(*)
            from p_video 
            where account_id=:account_id and is_deleted=0",
            [':account_id' => $accountId]
        )->queryScalar();
                
        return ['total' => $count, 'data' => $rows];
    }
    
    /**
     * 插入图文素材
     * @param array $mpnewsRowData
     * @param array $articles
     * @return array
     * @throws \Exception
     */
    public function insertMpnews($mpnewsRowData, $articles)
    {
        $preInsertActicels = [];
        $mpnewsRowData['id'] = StringHelper::uuid();
        $mpnewsRowData['created_on'] = DateTimeHelper::now();
        $mpnewsRowData['modified_on'] = $mpnewsRowData['created_on'];
        $mpnewsRowData['is_deleted'] = 0;
        $i = 1;
        foreach ($articles as $n) {
            $n['mpnews_id'] = $mpnewsRowData['id'];
            $n['id'] = StringHelper::uuid();
            $n['created_on'] = $mpnewsRowData['created_on'];
            $n['modified_on'] = $n['created_on'];
            $n['is_deleted'] = 0;
            $n['sort'] = $i++;
            $preInsertActicels[] = $n;
        }
        $conn = EntityBase::getDb();
        $trans = $conn->beginTransaction();
        try {
            $conn->createCommand()->insert('p_mpnews', $mpnewsRowData)->execute();
            foreach ($preInsertActicels as $articel) {
                $conn->createCommand()->insert('p_article', $articel)->execute();
            }
        } catch (\Exception $ex) {
            $trans->rollback();
            throw $ex;
        }
        
        $trans->commit();
        return $mpnewsRowData;
    }

    /**
     * 获取素材路径
     * @return mixed
     */
    public function getShareUrl($id, $accountId)
    {
        $url = WebUtility::createBeautifiedUrl('/wechat/picture/detail', ['id' => $id,"public_id" =>$accountId]);  
        return $url;
    }
    
    
    public function insertPictureGroup($accountId, $groupName, $createdBy)
    {
        $now = DateTimeHelper::now();
        $groupData = [
            'id' => StringHelper::uuid(),
            'account_id' => $accountId,
            'name' => $groupName,
            'created_by' => $createdBy,
            'created_on' => $now,
            'modified_by' => $createdBy,
            'modified_on' => $now,
            'is_deleted' => 0
        ];
    
        $conn = EntityBase::getDb();
        $conn->createCommand()->insert('p_picture_group', $groupData)->execute();
        return $groupData;
    }

    /**
     * 插入图片素材
     * @param array $picRowData p_picture数据行
     * @return array 数据行
     */
    public function insertPicture($picRowData)
    {
        $picRowData['id'] = StringHelper::uuid();
        $picRowData['created_on'] = DateTimeHelper::now();
        $picRowData['modified_on'] = $picRowData['created_on'];
        $picRowData['is_deleted'] = 0;
        $conn = EntityBase::getDb();
        $conn->createCommand()->insert('p_picture', $picRowData)->execute();
        return $picRowData;
    }
    
    /**
     * 插入语音素材
     * @param array $voiceRowData p_voice数据行
     * @return array 数据行
     */
    public function insertVoice($voiceRowData)
    {
        $voiceRowData['id'] = StringHelper::uuid();
        $voiceRowData['created_on'] = DateTimeHelper::now();
        $voiceRowData['modified_on'] = $voiceRowData['created_on'];
        $voiceRowData['is_deleted'] = 0;
        $conn = EntityBase::getDb();
        $conn->createCommand()->insert('p_voice', $voiceRowData)->execute();
        return $voiceRowData;
    }
    
    /**
     * 插入视频素材
     * @param array $videoRowData p_video数据行
     * @return array 数据行
     */
    public function insertVideo($videoRowData)
    {
        $videoRowData['id'] = StringHelper::uuid();
        $videoRowData['created_on'] = DateTimeHelper::now();
        $videoRowData['modified_on'] = $videoRowData['created_on'];
        $videoRowData['is_deleted'] = 0;
        $conn = EntityBase::getDb();
        $conn->createCommand()->insert('p_video', $videoRowData)->execute();
        return $videoRowData;
    }
    
    /**
     * 更新图文永久素材
     * @param type $id
     * @param type $mpnewsRowData
     * @param type $articlesRowData
     * @return type
     * @throws \Exception
     */
    public function updateMpnews($id, $mpnewsRowData, $articlesRowData)
    {
        $mpnewsRowData['modified_on'] = DateTimeHelper::now();
        $i = 1;
        $preInsertArticels = [];
        $preUpdateArtices = [];
        foreach ($articlesRowData as $articel) {
            $rowState = $articel['id'] ? "update" : "insert";
            $articel['mpnews_id'] = $id;
            $articel['modified_on'] = $mpnewsRowData['modified_on'];
            $articel['is_deleted'] = 0;
            $articel['sort'] = $i++;
            if ($rowState == "insert") {
                $articel['id'] = StringHelper::uuid();
                $articel['created_on'] = $mpnewsRowData['modified_on'];
                $preInsertArticels[] = $articel;
            } else {
                $preUpdateArtices[] = $articel;
            }
        }
        $conn = EntityBase::getDb();
        $trans = $conn->beginTransaction();
        try {
            $conn->createCommand()->update('p_mpnews', $mpnewsRowData, ['id' => $id])->execute();
            // 先删除文章－再插入新增的－再更新已有的同时把删除标记置回
            $conn->createCommand()->update('p_article', ['is_deleted' => 1], ['mpnews_id' => $id, 'is_deleted' => 0])->execute();
            if (count($preInsertArticels) > 0) {
                $conn->createCommand()->batchInsert(
                    'p_article',
                    ['id','title','author','cover_url','is_cover_showin_body',
                    'summary','body','original_url','share_point','created_by','modified_by','mpnews_id',
                    'modified_on','is_deleted','sort','created_on'],
                    $preInsertArticels
                )->execute();
            }
            
            foreach ($preUpdateArtices as $articel) {
                $conn->createCommand()->update('p_article', $articel, ['mpnews_id' => $id, 'id' => $articel['id']])->execute();
            }
        } catch (\Exception $ex) {
            $trans->rollback();
            throw $ex;
        }
        
        $trans->commit();
        return $mpnewsRowData;
    }
    
    /**
     * 更新图片分组
     * @param type $groupId
     * @param type $newName
     * @return type
     * @throws \InvalidArgumentException
     */
    public function updatePictureGroupName($groupId, $newName, $modifiedBy)
    {
        // 验证分组是否存在
        $groupInfo = $this->getPictureGroupInfo($groupId);
        if ($groupInfo === false) {
            throw new \InvalidArgumentException("无效的图片分组");
        }
        $groupInfo['name'] = $newName;
        $groupInfo['modified_by'] = $modifiedBy;
        $groupInfo['modified_on'] = DateTimeHelper::now();
        $conn = EntityBase::getDb();
        $affectRow = $conn->createCommand()->update('p_picture_group', $groupInfo, ['id' => $groupId])->execute();
        return $affectRow;
    }
    
    /**
     * 更新图片
     * @param type $id
     * @param type $pictureRowData
     * @return type
     * @throws \InvalidArgumentException
     */
    public function updatePicture($id, $pictureRowData)
    {
        // 验证分组是否存在
        if (array_key_exists('group_id', $pictureRowData)
            && !empty($pictureRowData['group_id'])
            && ($this->getPictureGroupInfo($pictureRowData['group_id'])) === false ) {
            throw new \InvalidArgumentException("无效的图片分组");
        }
        
        $pictureRowData['modified_on'] = \app\framework\utils\DateTimeHelper::now();
        $conn = EntityBase::getDb();
        $conn->createCommand()->update('p_picture', $pictureRowData, ['id' => $id])->execute();
        return $pictureRowData;
    }
    
    /**
     * 更新语音名称
     * @param type $id
     * @param array $newName
     * @return type
     */
    public function updateVoiceName($id, $newName, $modifiedBy)
    {
        $conn = EntityBase::getDb();
        $affectRow = $conn->createCommand()->update(
            'p_voice',
            ['modified_by' => $modifiedBy, 'name' => $newName],
            ['id' => $id]
        )->execute();
        
        return $affectRow;
    }
    
     /**
     * 更新视频
     * @param type $id
     * @param array $videoRowData
     * @return type
     */
    public function updateVedio($id, $videoRowData)
    {
        $videoRowData['modified_on'] = \app\framework\utils\DateTimeHelper::now();
        $conn = EntityBase::getDb();
        $affectRow = $conn->createCommand()->update('p_video', $videoRowData, ['id' => $id])->execute();
        
        return $affectRow;
    }
    
    /**
     * 根据media_id删除图文素材
     * @param type $mediaId
     * @param type $deletedBy
     * @return int
     * @throws \Exception
     */
    public function removeMpnewsByMediaId($mediaId, $deletedBy)
    {
        $conn = EntityBase::getDb();
        $trans = $conn->beginTransaction();
        try {
            // 根据media_id获取图文的id
            $id = $conn->createCommand("select id from p_mpnews where media_id =:media_id and is_deleted=0", [':media_id'=>$mediaId])->queryScalar();
            if ($id === false) {
                $trans->commit();
                return 0;
            }
            // 删除图文
            $affectedRow = $conn->createCommand()->update('p_mpnews', ['is_deleted' => 1, 'modified_by' => $deletedBy, 'modified_on' => DateTimeHelper::now()], ['id' => $id])->execute();
            // 删除文章
            $conn->createCommand()->update('p_article', ['is_deleted' => 1], ['mpnews_id' => $id])->execute();
        } catch (\Exception $ex) {
            $trans->rollback();
            throw $ex;
        }
        
        $trans->commit();
        return $affectedRow;
    }


    /**
     * 逻辑删除图文
     * @param type $id
     * @return type
     * @throws \Exception
     */
    public function removeMpnewsById($id, $deletedBy)
    {
        $conn = EntityBase::getDb();
        $trans = $conn->beginTransaction();
        try {
            // 删除图文
            $affectedRow = $conn->createCommand()->update('p_mpnews', ['is_deleted' => 1, 'modified_by' => $deletedBy, 'modified_on' => DateTimeHelper::now()], ['id' => $id])->execute();
            // 删除文章
            $conn->createCommand()->update('p_article', ['is_deleted' => 1], ['mpnews_id' => $id])->execute();
        } catch (\Exception $ex) {
            $trans->rollback();
            throw $ex;
        }
        
        $trans->commit();
        return $affectedRow;
    }
    
    /**
     * 逻辑删除图片
     * @param type $id
     * @return type
     */
    public function removePictureById($id, $deletedBy)
    {
        $conn = EntityBase::getDb();
        $affectedRow = $conn->createCommand()->update('p_picture', ['is_deleted' => 1, 'modified_by' => $deletedBy, 'modified_on' => DateTimeHelper::now()], ['id' => $id])->execute();
        return $affectedRow;
    }
    
    /**
     * 逻辑删除图片分组
     * @param type $groupId
     * @return type
     * @throws \Exception
     */
    public function removePictureGroup($groupId, $deletedBy)
    {
        $conn = EntityBase::getDb();
        $trans = $conn->beginTransaction();
        try {
            $conn->createCommand()->update('p_picture', ['group_id' => null], ['group_id' => $groupId])->execute();
            $affectedRow = $conn->createCommand()->update('p_picture_group', ['is_deleted' => 1, 'modified_by' => $deletedBy, 'modified_on' => DateTimeHelper::now()], ['id' => $groupId])->execute();
        } catch (\Exception $ex) {
            $trans->rollback();
            throw $ex;
        }
        
        $trans->commit();
        
        return $affectedRow;
    }
    
     /**
     * 逻辑删除语音
     * @param type $id
     * @return type
     */
    public function removeVoiceById($id, $deletedBy)
    {
        $conn = EntityBase::getDb();
        $affectedRow = $conn->createCommand()->update('p_voice', ['is_deleted' => 1, 'modified_by' => $deletedBy, 'modified_on' => DateTimeHelper::now()], ['id' => $id])->execute();
        return $affectedRow;
    }
    
    /**
     * 逻辑删除语音
     * @param type $id
     * @return type
     */
    public function removeVideoById($id, $deletedBy)
    {
        $conn = EntityBase::getDb();
        $affectedRow = $conn->createCommand()->update('p_video', ['is_deleted' => 1, 'modified_by' => $deletedBy, 'modified_on' => DateTimeHelper::now()], ['id' => $id])->execute();
        return $affectedRow;
    }
    
    /**
     * 获取图片分组信息
     * @param type $id
     * @return type
     */
    public function getPictureGroupInfo($id)
    {
        $query = new \yii\db\Query();
        $row = $query->from('p_picture_group')
                ->where(['id' => $id, 'is_deleted'=>0])
                ->select('name')
                ->createCommand(EntityBase::getDb())
                ->queryOne();
        
        return $row;
    }

    /**
     * 根据图片url获取media_id
     * @param guid $accountId
     * @param string $imgUrl
     * @return string media_id查找不到返回''
     */
    public function getPicMediaIdByUrl($accountId, $imgUrl)
    {
        $query = new \yii\db\Query();
        $mediaId = $query->from('p_picture')
                ->where(['img_url' => $imgUrl, 'account_id' => $accountId, 'is_deleted'=>0])
                ->select('media_id')
                ->createCommand(EntityBase::getDb())
                ->queryScalar();
        
        return $mediaId?:'';
    }
    
    /**
     * 根据Media_id获取图文的相关数据
     * @param type $mediaId
     * @return array
     */
    public function getMpnewsInfoByMediaId($mediaId)
    {
        $query = new \yii\db\Query();
        $row = $query->from('p_mpnews')
                ->where(['media_id' => $mediaId, 'is_deleted'=>0])
                ->select('media_id,account_id, id, modified_on')
                ->createCommand(EntityBase::getDb())
                ->queryOne();
        
        return $row;
    }
    
    /**
     * 获取图文的media_id
     * @param guid $id
     * @return string
     */
    public function getMpnewsInfoById($id)
    {
        $query = new \yii\db\Query();
        $row = $query->from('p_mpnews')
                ->where(['id' => $id, 'is_deleted'=>0])
                ->select('media_id,account_id, id, modified_on')
                ->createCommand(EntityBase::getDb())
                ->queryOne();
        
        return $row;
    }
    
    /**
     * 获取图片信息
     * @param type $id
     * @return type
     */
    public function getPictureInfoById($id)
    {
        $query = new \yii\db\Query();
        $row = $query->from('p_picture')
                ->where(['id' => $id, 'is_deleted'=>0])
                ->select('name,media_id,account_id,img_url,wechat_url')
                ->createCommand(EntityBase::getDb())
                ->queryOne();
        
        return $row;
    }
    
    /**
     * 获取语音信息
     * @param type $id
     * @return type
     */
    public function getVoiceInfoById($id)
    {
        $query = new \yii\db\Query();
        $row = $query->from('p_voice')
                ->where(['id' => $id, 'is_deleted'=>0])
                ->select('name,media_id,account_id,voice_url')
                ->createCommand(EntityBase::getDb())
                ->queryOne();
        
        return $row;
    }
    
    /**
     * 获取视频信息
     * @param type $id
     * @return type
     */
    public function getVideoInfoById($id)
    {
        $query = new \yii\db\Query();
        $row = $query->from('p_video')
                ->where(['id' => $id, 'is_deleted'=>0])
                ->select('title,summary,type,media_id,account_id,video_url')
                ->createCommand(EntityBase::getDb())
                ->queryOne();
        
        return $row;
    }
}

