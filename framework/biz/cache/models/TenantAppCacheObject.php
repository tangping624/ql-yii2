<?php

namespace app\framework\biz\cache\models;

use app\framework\cache\CacheObject;

/**
 * 租户App集合
 * Class TenantAppCacheObject
 * @package app\framework\biz\cache\models
 */
class TenantAppCacheObject extends CacheObject
{
    /**
     * @var string 租户代码 对应mycommunity_config.tenant.`code`
     */
    public $id;

    /**
     * @var array 租户应用代码集合
     */
    public $appCodeList = [];
    
}
