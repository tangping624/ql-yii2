<?php
namespace app\framework\settings\interfaces;

/**
 * 系统参数读写接口
 */
interface SettingsAccessorInterface
{
    /**
     * 获取参数, 如果不存在则返回null
     * @param $key
     * @return null|mixed
     */
    public function get($key);

    /**
     * 保存参数设置, 不能保存null
     * @param string $key
     * @param mixed $value
     * @param string $description 描述
     * @return bool
     */
    public function set($key, $value, $description);
}