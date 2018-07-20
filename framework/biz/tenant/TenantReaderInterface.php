<?php

namespace app\framework\biz\tenant;

interface TenantReaderInterface
{
    const TENANT_QUERY_STRING_KEY = 'o';
 


    /**
     * 当前或设置用户微信openid
     * @param string $openid 如果不为空,则设置当前缓存的openid
     * @return string
     */
    public function getOpenId($openid='');


    /**
     * 公众号id
     * @return string
     */
    public function getPublicId();
}