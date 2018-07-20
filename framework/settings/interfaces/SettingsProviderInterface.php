<?php
namespace app\framework\settings\interfaces;

/**
 * 数据存储接口
 */
interface SettingsProviderInterface
{
    /**
     * 新增或更新, $value不能为null
     * @param string $key
     * @param mixed $value
     * @param string $description 描述
     * @return bool
     */
    public function upset($key, $value, $description);

    /**
     * 获取配置项
     * @param string $key
     * @return array|null
     */
    public function get($key);
}