<?php

namespace app\framework\biz\cache\models;

use app\framework\cache\CacheObject;

class FanCacheObject extends CacheObject
{
    public $id;//openid;
    public $fanId;
    public $sex;
    public $nickName;
    public $memberId;
    public $name;
    public $headimgUrl;
    public $corpId;
    public $fanCorpId;
    public $isFollowed;

    public static function get_cacheId($id, $scope = 1, $scopeId = '')
    {
        return parent::get_cacheId('member:openid:' . $id, 0, $scopeId);
    }
}