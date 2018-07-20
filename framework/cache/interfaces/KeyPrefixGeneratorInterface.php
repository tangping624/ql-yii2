<?php

namespace app\framework\cache\interfaces;

interface KeyPrefixGeneratorInterface
{
    /**
     * 分隔符
     */
    const KEY_SEPARATOR = ':';

    /**
     * 生成缓存的key的前缀
     * @param int $scope 缓存参数范围
     *  全局缓存,key只由$id决定 
     * @param string $scopeId 对应范围的标识
     * @return string
     */
    public function createKeyPrefix($scope = 1, $scopeId = '');
}