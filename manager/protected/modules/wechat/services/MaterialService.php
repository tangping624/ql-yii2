<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\wechat\services;

use app\framework\biz\cache\SiteCacheManager;
use app\modules\ServiceBase;
use app\modules\wechat\repositories\MaterialRepository;
use app\framework\weixin\helper\Article;
use app\framework\weixin\AccessTokenHelper;
use app\modules\wechat\repositories\AccountRepository;
use app\framework\weixin\proxy\fw\Material;


/**
 * Description of MaterialService
 *
 * @author Chenxy
 */
class MaterialService extends ServiceBase
{
    private $_materialRepository;
    private $_accountRepository;
    public function __construct(MaterialRepository $materialRepository, AccountRepository $accountRepository)
    {
        $this->_materialRepository = $materialRepository;
        $this->_accountRepository = $accountRepository;
    }
    
    /**
     * 获取图文列表
     * @param guid $accountId
     * @return array [ 'total' => 总记录数， 'data' =>
     *                [
     *                  ['id'=>图文id,'modified_on'=>最后修改时间,'media_id'=>media_id,'articles'=>['title'=>标题,'cover_url'=>封面,'is_cover_showin_body'=>1显示封面0不显示,'body'=>正文,'summary'=>摘要,'original_url'=>原文url,'author'=>作者], [], ..., []],
     *               ]
     *          ]
     */
    public function getMpnewsList($accountId, $keyword, $offset, $limit)
    {
        $mpnews = [];
        $news = $this->_materialRepository->getMpnewsList($accountId, $keyword, $offset, $limit);
        foreach ($news['data'] as $n) {
            $articles = $this->_materialRepository->getNewsArticlesBy($n['id']);
            for ($i = 0; $i < count($articles); $i++) {
                $articles[$i]["body"] = $this->dealImgSrc($articles[$i]["body"]);
                $articles[$i]["share_url"] = $this->_materialRepository->getShareUrl($articles[$i]["id"], $accountId);
            }
            $n['articles'] = $articles;
            $mpnews[] = $n;
        }
        
        return ['total' => $news['total'], 'data' => $mpnews];
    }

    private function dealImgSrc($body)
    {
        preg_match_all('/<img.*?src=[\'"]([^>\'"]+)[\'"][^>]*>/i', $body, $match);

        if (!empty($match[1])) {
            $arr = [];
            $commonImageUrl = '/wechat/image/image';
            foreach ($match[1] as $src) {
                if (strpos($src, $commonImageUrl) !== false) {
                    continue;
                }

                $arr[$src] = $commonImageUrl . '?src=' . urlencode($src);
            }

            $srcs = array_keys($arr);
            $dests = array_values($arr);

            $body = str_replace($srcs, $dests, $body);
        }

        return $body;
    }

    private function recoveryImgSrc($body)
    {
        preg_match_all('/<img.*?src=[\'"]([^>\'"]+)[\'"][^>]*>/i', $body, $match);

        if (!empty($match[1])) {
            $arr = [];
            $commonImageUrl = '/wechat/image/image';
            foreach ($match[1] as $src) {
                if (strpos($src, $commonImageUrl) === false) {
                    continue;
                }

                $arr[$src] = urldecode(str_replace($commonImageUrl.'?src=', '', $src));
            }

            $srcs = array_keys($arr);
            $dests = array_values($arr);

            $body = str_replace($srcs, $dests, $body);
        }

        return $body;
    }
    
    /**
     * 获取图片分组列表
     * @param type $accountId
     * @return type
     */
    public function getPictureGroupList($accountId)
    {
        return $this->_materialRepository->getPictureGroupList($accountId);
    }
    
    /**
     * 获取对应分组的图片列表，$groupid为空表示取未分组的图片
     * @param type $accountId
     * @param type $groupId
     * @param type $offset
     * @param type $limit
     * @return array ['total' => 总记录数，'data' => rows]
     */
    public function getPictureList($accountId, $groupId, $offset, $limit)
    {
        $data = $this->_materialRepository->getPictureList($accountId, $groupId, $offset, $limit);
        return $data;
    }
    
    /**
     * 获取语音列表
     * @param type $accountId
     * @param type $offset
     * @param type $limit
     * @return array ['total' => 总记录数，'data' => rows]
     */
    public function getVoiceList($accountId, $offset, $limit)
    {
        $data = $this->_materialRepository->getVocieList($accountId, $offset, $limit);
        return $data;
    }
    
    /**
     * 获取视频列表
     * @param type $accountId
     * @param type $offset
     * @param type $limit
     * @return array ['total' => 总记录数，'data' => rows]
     */
    public function getVideoList($accountId, $offset, $limit)
    {
        $data = $this->_materialRepository->getVideoList($accountId, $offset, $limit);
        return $data;
    }
    
    /**
     * @param guid $accountId 公众号id
     * @param guid $id  PK 为空时新增
     * @param string $type 图文（news），图片（image）、语音（voice）、视频（video）和缩略图（thumb）
     * @param array $materialData
     * 图文：[{'title':标题,'cover_url':封面图片url,'cover_name':封面图片文件名（含扩展名）,'is_cover_showin_body':1显示封面0不显示,'body':正文,'summary':摘要,'original_url':原文url,'author':作者},多图文有多个]
     * 图片：新增：{'name':图片名称，'img_url':图片路径url,'img_name':图片文件名（含扩展名）, 'group_id':图片分组，未分组传空}
     *      修改： {'name':图片名称, 'group_id':图片分组，未分组传空} 修改了哪项传哪项
     * 语音：新增：{'name':语音名称，'voice_url':语音路径url,'voice_name':语音文件名（含扩展名）}
     *      修改： {'name':语音名称}
     * 视频：新增 {'title':标题，'summary':简介, 'video_url':视频路径url,'video_name':视频文件名（含扩展名）, 'type':固定值：本地}
     *      修改：{'title':标题，'summary':简介} 修改了哪项传哪项
     * @return id
     * @throws \InvalidArgumentException
     */
    public function saveMaterial($accountId, $id, $type, $materialData, $operatorId = null)
    {
        $materialDataArrayFormat = [];
        switch (strtolower($type)) {
            case 'news':
                foreach ($materialData as $value) {
                    $materialDataArrayFormat[] = (array)$value;
                }
                $id =$this->saveMpnews($accountId, $id, $materialDataArrayFormat, $operatorId);
                break;
            case 'picture':
                $id = $this->savePicture($accountId, $id, (array)$materialData, $operatorId);
                break;
            case 'voice':
                $id = $this->saveVoice($accountId, $id, (array)$materialData, $operatorId);
                break;
            case 'video':
                $id = $this->saveVideo($accountId, $id, (array)$materialData, $operatorId);
                break;
            default :
                throw new \InvalidArgumentException('参数type无效');
        }
        
        return $id;
    }
    
    /**
     * 保存图文
     * @param type $accountId
     * @param type $id
     * @param type $newsData
     * @return type
     */
    private function saveMpnews($accountId, $id, $newsData, $operatorId)
    {
        // 新增图文
        if (empty($id)) {
            $id = $this->addMpnews($accountId, $newsData, $operatorId);
            return $id;
        }
        
        // 修改图文
        $this->updateMpnews($accountId, $id, $newsData, $operatorId);
        return $id;
    }
    
    public function addMpnews($accountId, $newsData, $operatorId)
    {
        $articels = [];
        // 转换
        foreach ($newsData as $news) {
            $mediaId = $this->_materialRepository->getPicMediaIdByUrl($accountId, $news['cover_url']);
            $articels[] = $this->convertToArticel($accountId, $news, $operatorId, $mediaId);
        }

        // 上传微信并转换错误提示
        $api = $this->getWeixinMaterialProxy($accountId);
        try {
            $apiRetrunData = $api->addNews($articels);
        } catch (\app\framework\weixin\WeixinException $ex) {
            if ($ex->getCode() == 45034) {
                throw new Exception("超过微信图文素材个数上限（5000），请先删除部分图文，再重试");
            }
            
            throw $ex;
        }
        
        // 写入图文库
        $mpnewsRowData = [
            'account_id' => $accountId,
            'media_id' => $apiRetrunData->media_id,
            'created_by' => $operatorId,
            'modified_by' => $operatorId
        ];
        
        $articleDataRows = [];
        foreach ($newsData as $value) {
            $value = $this->removeElement($value, 'cover_name');
            $value['created_by'] = $operatorId;
            $value['modified_by'] = $operatorId;
            $value['body'] = $this->recoveryImgSrc($value['body']);
            $articleDataRows[] = $value;
        }
        try {
            $data = $this->_materialRepository->insertMpnews($mpnewsRowData, $articleDataRows);
        } catch (\Exception $ex) {
            $api->delMaterial($apiRetrunData->media_id);
            throw $ex;
        }
        
        return $data['id'];
    }
    
    /**
     * 更新图文
     * @param guid $accountId 公众号id
     * @param guid $id
     * @param array $newsData
     */
    private function updateMpnews($accountId, $id, $newsData, $operatorId)
    {
        $this->validateMaterialExists($id, 'news');
        $articels = [];
        // 图片更改了则重新上传修改的图片
        foreach ($newsData as $news) {
            $mediaId = $this->_materialRepository->getPicMediaIdByUrl($accountId, $news['cover_url']);
            $articels[] = $this->convertToArticel($accountId, $news, $operatorId, $mediaId);
        }
        
        $api = $this->getWeixinMaterialProxy($accountId);
        // 获取mediaId并上传修改
        $mpnewsInfo= $this->_materialRepository->getMpnewsInfoById($id);
        if (!empty($mpnewsInfo['media_id'])) {
            $api->delMaterial($mpnewsInfo['media_id']);
        }
        $apiRetrunData = $api->addNews($articels);
        
        // 写入数据库
        $mpnewsDataRow = [
            'media_id' => $apiRetrunData->media_id,
            'modified_by' => $operatorId
        ];
        
        $articleDataRows = [];
        foreach ($newsData as $value) {
            $value = $this->removeElement($value, 'cover_name');
            $value['created_by'] = $operatorId;
            $value['modified_by'] = $operatorId;
            $value['body'] = $this->recoveryImgSrc($value['body']);
            $articleDataRows[] = $value;
        }
        $this->_materialRepository->updateMpnews($id, $mpnewsDataRow, $articleDataRows);
        return $id;
    }
    
    private function removeElement($input, $searchKey)
    {
        $return = [];
        if (is_string($searchKey)) {
            foreach ($input as $key => $value) {
                if (strtolower($searchKey) == $key) {
                    continue;
                }
                
                $return[$key] = $value;
            }
        }
        
        return $return;
    }
    
    /**
     * 转换为图文对象,若新的图片会自动上传到微信并记录到本地图片库
     * @param guid $accountId
     * @param array $articelDataRow  一条图片文章
     * @param string $picMediaId  图文的图片media_id
     * @return Article
     * @throws \InvalidArgumentException
     */
    private function convertToArticel($accountId, $articelDataRow, $operatorId, $picMediaId = '')
    {
        // 校验
        if (!array_key_exists('title', $articelDataRow)
            || !array_key_exists('cover_url', $articelDataRow)
            || !array_key_exists('body', $articelDataRow)
        ) {
            throw new \InvalidArgumentException('参数无效');
        }
       
        // 转换为接口参数
        $acticel = new Article();
        $acticel->title = $articelDataRow['title'];
        $acticel->author = $articelDataRow['author'];
        $acticel->content = $this->recoveryImgSrc($articelDataRow['body']);
        $acticel->content_source_url = $articelDataRow['original_url'];
        $acticel->digest = array_key_exists('summary', $articelDataRow) ?  $articelDataRow['summary'] : null;
        $acticel->show_cover_pic = $articelDataRow['is_cover_showin_body'];
        // 上传图文并写入图片库
        if (empty($picMediaId)) {
            $row = $this->addPicture($accountId, ['img_url' => $articelDataRow['cover_url'], 'name' => '', 'img_name' => $articelDataRow['cover_name']], $operatorId);
            $picMediaId = $row['media_id'];
        }
        $acticel->thumb_media_id = $picMediaId;
        return $acticel;
    }
    
    /**
     * 新增图片分组
     * @param type $accountId
     * @param type $groupName
     * @return type
     */
    public function addPictureGroup($accountId, $groupName, $operatorId)
    {
        $row = $this->_materialRepository->insertPictureGroup($accountId, $groupName, $operatorId);
        return $row['id'];
    }
    
    /**
     * 修改图片分组
     * @param type $groupId
     * @param type $newName
     * @return type
     */
    public function updatePictureGroup($groupId, $newName, $operatorId)
    {
        return $this->_materialRepository->updatePictureGroupName($groupId, $newName, $operatorId);
    }

    /**
     * 保存图片素材
     * @param type $accountId
     * @param type $id
     * @param type $pictureData
     * @return type
     */
    private function savePicture($accountId, $id, $pictureData, $operatorId)
    {
        // 新增图片
        if (empty($id)) {
            $id = $this->addPicture($accountId, $pictureData, $operatorId);
            return $id;
        }
        
        // 编辑图片（图片名称或分组）
        $this->updatePicture($id, $pictureData, $operatorId);
        return $id;
    }
    
    /**
     * 新增永久图片素材
     * @param type $accountId
     * @param type $pictureRowData ['name'=>图片名称，'img_url'=>图片路径url, 'img_name'=>图片文件名,'group_id'=>图片分组，未分组传空或不传]
     * @throws \Exception
     */
    private function addPicture($accountId, $pictureRowData, $operatorId)
    {
        // 从图片服务器拉取图片到本地
        $pictureUrl = $pictureRowData['img_url'];
        $pictureName = $pictureRowData['name'];
        $pictureFileName = $pictureRowData['img_name'];
        if (empty($pictureFileName)) {
            $pictureFileName = substr($pictureUrl, strrpos($pictureUrl, '/') + 1);
        }
        $file = $_SERVER ['DOCUMENT_ROOT'] . "/temp/weixin/" . $pictureFileName;
        is_file($file) && unlink($file);
        if (!copy($pictureUrl, $file)) {
            throw new \Exception("下载文件{$pictureUrl}失败");
        }
        
        //  上传图片到微信
        $api = $this->getWeixinMaterialProxy($accountId);
        try {
            $apiRetrunData = $api->addMaterial('image', $file);
        } catch (\app\framework\weixin\WeixinException $ex) {
            if ($ex->getCode() == 45034) {
                throw new Exception("超过微信图片素材个数上限（5000），请先删除部分图片，再重试");
            }
            
            throw $ex;
        }
        
        // 写入本地图片库
        try {
            $imgData = $this->_materialRepository->insertPicture([
                'name' => $pictureName ?: $this->getFileName($pictureFileName),
                'img_url' => $pictureUrl,
                'group_id' => (!array_key_exists('group_id', $pictureRowData) || empty($pictureRowData['group_id'])) ? null : $pictureRowData['group_id'],
                'wechat_url' => $apiRetrunData->url,
                'media_id' => $apiRetrunData->media_id,
                'account_id' => $accountId,
                'created_by' => $operatorId,
                'modified_by' => $operatorId
            ]);
        } catch (\Exception $ex) {
            $api->delMaterial($apiRetrunData->media_id);
            throw $ex;
        }
        
        return $imgData;
    }
    
    /**
     * 修改图片素材
     * @param type $id
     * @param type $pictureData
     * @return type
     */
    private function updatePicture($id, $pictureData, $operatorId)
    {
        $this->validateMaterialExists($id, 'picture');
        $pictureData['modified_by'] = $operatorId;
        $pictureData['group_id'] = array_key_exists('group_id', $pictureData) ?  ($pictureData['group_id']?:null) : null;
        $this->_materialRepository->updatePicture($id, $pictureData);
        return $id;
    }
    
    /**
     * 保存语音
     * @param type $accountId
     * @param type $id
     * @param type $voiceData
     * @return type
     */
    private function saveVoice($accountId, $id, $voiceData, $operatorId)
    {
        // 新增语音
        if (empty($id)) {
            $id = $this->addVoice($accountId, $voiceData, $operatorId);
            return $id;
        }
        
        // 编辑语音名称
        $this->updateVoice($id, $voiceData['name'], $operatorId);
        return $id;
    }
    
    /**
     * ['name'=>语音名称，'voice_url'=>语音路径url]
     * @param type $accountId
     * @param type $voiceRowData
     */
    private function addVoice($accountId, $voiceRowData, $operatorId)
    {
        // 从服务器拉取语音到本地
        $voiceUrl = $voiceRowData['voice_url'];
        $vocieName = $voiceRowData['name'];
        $voiceFileName = $voiceRowData['voice_name'];
        $file = $_SERVER ['DOCUMENT_ROOT'] . "/temp/weixin/" . $voiceFileName;
        if (!copy($voiceUrl, $file)) {
            throw new \Exception("下载文件{$voiceUrl}失败");
        }
        
        //  上传到微信
        $api = $this->getWeixinMaterialProxy($accountId);
        try {
            $apiRetrunData = $api->addMaterial('voice', $file);
        } catch (\app\framework\weixin\WeixinException $ex) {
            if ($ex->getCode() == 45034) {
                throw new Exception("超过微信音频素材个数上限（1000），请先删除部分音频，再重试");
            }
            
            throw $ex;
        }
        
        // 写入本地语音库
        try {
            $voiceData = $this->_materialRepository->insertVoice([
                'name' => $vocieName ?: $this->getFileName($voiceFileName),
                'voice_url' => $voiceUrl,
                'media_id' => $apiRetrunData->media_id,
                'account_id' => $accountId,
                'created_by' => $operatorId,
                'modified_by' => $operatorId
            ]);
        } catch (\Exception $ex) {
            $api->delMaterial($apiRetrunData->media_id);
            throw $ex;
        }
        return $voiceData;
    }
    
    /**
     * 修改语音名称
     * @param type $id
     * @param type $newName
     * @return type
     */
    private function updateVoice($id, $newName, $operatorId)
    {
        $this->validateMaterialExists($id, 'voice');
        $this->_materialRepository->updateVoiceName($id, $newName, $operatorId);
        return $id;
    }

    /**
     * 保存视频
     * @param type $accountId
     * @param type $id
     * @param type $videoData
     * @return type
     */
    private function saveVideo($accountId, $id, $videoData, $operatorId)
    {
        // 新增视频
        if (empty($id)) {
            $id = $this->addVideo($accountId, $videoData, $operatorId);
            return $id;
        }
        
        // 编辑视频(标题、简介)
        $this->updateVideo($id, $videoData, $operatorId);
        return $id;
    }
    
    /**
     * ['title'=>标题，'summary'=>简介, 'video_url'=>视频路径url, 'type'=>固定值：本地]
     * @param type $accountId
     * @param type $videoRowData
     */
    private function addVideo($accountId, $videoRowData, $operatorId)
    {
        // 从服务器拉取语音到本地
        $videoUrl = $videoRowData['video_url'];
        $videoTitle = $videoRowData['tile'];
        $videoSummary = $videoRowData['summary'];
        $videoType = $videoRowData['type'];
        $videoFileName = $videoRowData['video_name'];
        $file = $_SERVER ['DOCUMENT_ROOT'] . "/temp/weixin/" . $videoFileName;
        if (!copy($videoUrl, $file)) {
            throw new \Exception("下载文件{$videoUrl}失败");
        }
        
        //  上传到微信
        $api = $this->getWeixinMaterialProxy($accountId);
        try {
            $apiRetrunData = $api->addMaterial('video', $file, $videoTitle, $videoSummary);
        } catch (\app\framework\weixin\WeixinException $ex) {
            if ($ex->getCode() == 45034) {
                throw new Exception("超过微信视频素材个数上限（1000），请先删除部分视频，再重试");
            }
            
            throw $ex;
        }
        
        // 写入本地视频库
        try {
            $videoData = $this->_materialRepository->insertVideo([
                'title' => $videoTitle,
                'summary' => $videoSummary,
                'type' => $videoType,
                'video_url' => $videoUrl,
                'media_id' => $apiRetrunData->media_id,
                'account_id' => $accountId,
                'created_by' => $operatorId,
                'modified_by' => $operatorId
            ]);
        } catch (\Exception $ex) {
            $api->delMaterial($apiRetrunData->media_id);
            throw $ex;
        }
        
        return $videoData;
    }
    
    /**
     * 修改视频标题、简介
     * @param type $id
     * @param type $videoRowData
     * @return type
     */
    private function updateVideo($id, $videoRowData, $operatorId)
    {
        $this->validateMaterialExists($id, 'voice');
        $videoRowData['modified_by'] = $operatorId;
        $this->_materialRepository->updateVedio($id, $videoRowData);
        return $id;
    }

    /**
     * 获取图文基本信息
     * @param type $id
     * @return type
     */
    public function getMpnewsInfo($id)
    {
        return $this->_materialRepository->getMpnewsInfoById($id);
    }
    
    /**
     * 获取图片基本信息
     * @param type $id
     * @return type
     */
    public function getPictureInfo($id)
    {
        return $this->_materialRepository->getPictureInfoById($id);
    }
    
    /**
     * 获取语音基本信息
     * @param type $id
     * @return type
     */
    public function getVoiceInfo($id)
    {
        return $this->_materialRepository->getVoiceInfoById($id);
    }

    /**
     * 获取视频基本信息
     * @param type $id
     * @return type
     */
    public function getVideoInfo($id)
    {
        return $this->_materialRepository->getVideoInfoById($id);
    }
    
    /**
     * 获取图文内容列表，用于编辑和查看图文素材
     * @param guid $id 图文PK
     * @return array
     */
    public function getArticlesById($id)
    {
        $articles = $this->_materialRepository->getNewsArticlesBy($id);
        return $articles;
    }

    /**
     * 获取图文内容列表，用于编辑和查看图文素材
     * @param guid $id 图文PK
     * @return array
     */
    public function getShareArticlesById($id, $accountId)
    {
        $articles = $this->_materialRepository->getNewsArticlesBy($id);
        for ($i=0; $i<count($articles); $i++) {
            $articles[$i]["share_url"] = $this->_materialRepository->getShareUrl($articles[$i]["id"], $accountId);
            $articles[$i]["body"] = $this->dealImgSrc($articles[$i]["body"]);
        }
        return $articles;
    }


    /**
     * 删除永久素材
     * @param guid $id PK
     * @param string $type 图文（news），图片（image）、语音（voice）、视频（video）
     * @throws \InvalidArgumentException
     */
    public function deleteMaterial($id, $type, $operatorId)
    {
        switch (strtolower($type)) {
            case 'news':
                $affectedRow = $this->delelteMpnews($id, $operatorId);
                break;
            case 'picture':
                $affectedRow = $this->deletePicture($id, $operatorId);
                break;
            case 'voice':
                $affectedRow = $this->deleteVoice($id, $operatorId);
                break;
            case 'video':
                $affectedRow = $this->deleteVideo($id, $operatorId);
                break;
            default :
                throw new \InvalidArgumentException('参数type无效');
        }
        
        return $affectedRow;
    }
    
    /**
     * 删除图片分组
     * @param type $groupId
     * @return type
     */
    public function deletePictureGroup($groupId, $operatorId)
    {
        return $this->_materialRepository->removePictureGroup($groupId, $operatorId);
    }
    
    /**
     * 删除图文素材（同时从微信中删除）
     * @param type $id
     */
    private function delelteMpnews($id, $operatorId)
    {
        // 校验并取出图文media_id
        $mpnewsInfo= $this->validateMaterialExists($id, 'news');
        // 从数据库中删除
        $affectedRow = $this->_materialRepository->removeMpnewsById($id, $operatorId);
        // 从微信中删除
        if ($mpnewsInfo['media_id']) {
            $apiProxy = $this->getWeixinMaterialProxy($mpnewsInfo['account_id']);
            $apiProxy->delMaterial($mpnewsInfo['media_id']);
        }
        
        return $affectedRow;
    }
    
    /**
     * 删除图片素材（同时从微信中删除）
     * @param type $id
     * @return type
     */
    private function deletePicture($id, $operatorId)
    {
        // 校验并取出图片media_id
        $pictureInfo= $this->validateMaterialExists($id, 'picture');
        // 从数据库中删除
        $affectedRow = $this->_materialRepository->removePictureById($id, $operatorId);
        // 从微信中删除
        if ($pictureInfo['media_id']) {
            $apiProxy = $this->getWeixinMaterialProxy($pictureInfo['account_id']);
            $apiProxy->delMaterial($pictureInfo['media_id'], $operatorId);
        }
        
        return $affectedRow;
    }
    
    /**
     * 删除语音素材（同时从微信中删除）
     * @param type $id
     * @return type
     */
    private function deleteVoice($id, $operatorId)
    {
        // 校验并取出media_id
        $voiceInfo= $this->validateMaterialExists($id, 'voice');
        // 从数据库中删除
        $affectedRow = $this->_materialRepository->removeVoiceById($id, $operatorId);
        // 从微信中删除
        if ($voiceInfo['media_id']) {
            $apiProxy = $this->getWeixinMaterialProxy($voiceInfo['account_id']);
            $apiProxy->delMaterial($voiceInfo['media_id']);
        }
        
        return $affectedRow;
    }
    
    /**
     * 删除视频素材（同时从微信中删除）
     * @param type $id
     * @return type
     */
    private function deleteVideo($id, $operatorId)
    {
        // 校验并取出media_id
        $voiceInfo= $this->validateMaterialExists($id, 'video');
        // 从数据库中删除
        $affectedRow = $this->_materialRepository->removeVideoById($id, $operatorId);
        // 从微信中删除
        if ($voiceInfo['media_id']) {
            $apiProxy = $this->getWeixinMaterialProxy($voiceInfo['account_id']);
            $apiProxy->delMaterial($voiceInfo['media_id']);
        }
        
        return $affectedRow;
    }
    
    /**
     * 校验
     * @param type $id
     * @param type $type
     * @return type
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    private function validateMaterialExists($id, $type)
    {
        switch (strtolower($type)) {
            case 'news':
                $materialInfo = $this->getMpnewsInfo($id);
                if ($materialInfo === false) {
                    throw new \Exception("图文{$id}不存在，可能已删除");
                }
                break;
            case 'picture':
                $materialInfo = $this->getPictureInfo($id);
                if ($materialInfo === false) {
                    throw new \Exception("图片{$id}不存在，可能已删除");
                }
                break;
            case 'voice':
                $materialInfo = $this->getVoiceInfo($id);
                if ($materialInfo === false) {
                    throw new \Exception("语音{$id}不存在，可能已删除");
                }
                break;
            case 'video':
                $materialInfo = $this->getVideoInfo($id);
                if ($materialInfo === false) {
                    throw new \Exception("视频{$id}不存在，可能已删除");
                }
                break;
            default :
                throw new \InvalidArgumentException('参数type无效');
        }
        
        return $materialInfo;
    }

    /**
     * 获取永久素材微信接口代理
     * @param type $accountId
     * @return Material
     * @throws \Exception
     */
    private function getWeixinMaterialProxy($accountId)
    {
        $originalId = $this->_accountRepository->getWeChatOriginalId($accountId);
        $accessTokenRepository = \Yii::$container->get('app\framework\weixin\interfaces\IAccessTokenRepository');
        if (!isset($accessTokenRepository)) {
            throw new \Exception('未注入app\framework\weixin\interfaces\IAccessTokenRepository实例');
        }
        $accessTokenHelper = new AccessTokenHelper($originalId, $accessTokenRepository);
        $apiProxy = new Material($accessTokenHelper);

        return $apiProxy;
    }
    
    /**
     * 提取文件名
     * @param string $file bird.jpeg
     * @return string 文件名 bird
     */
    private function getFileName($file)
    {
        $posIndex = strrpos($file, '.');
        $name = substr($file, 0, $posIndex);
        return $name;
    }
}
