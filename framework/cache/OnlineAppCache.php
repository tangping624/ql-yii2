<?php

namespace app\framework\cache;

//缓存当前在线应用
class OnlineAppCache
{
    /**
     * @param string $sid
     * @param array $data
     * ['logoutUrl'=>, 'sessionId'=>]
     */
    public static function cacheLogoutData($sid, $data)
    {
        if (empty($sid)) {
            throw new \InvalidArgumentException('$sid');
        }
        if (isset($data)) {
            $key = static::_key($sid);
            $cached = \Yii::$app->cache->get($key);
            $cached = $cached == false ?  [] : $cached;
            array_push($cached, $data);
            \Yii::$app->cache->set($key, $cached, 604800);
        }

    }

    /**
     * @param string $sid
     */
    public static function removeLogoutData($sid)
    {
        if (empty($sid)) {
            throw new \InvalidArgumentException('$sid');
        }

        $key = static::_key($sid);
        \Yii::$app->cache->delete($key);
    }

    /**
     * @param $sid
     * @return null|array  ['logoutUrl'=>, 'sessionId'=>]
     */
    public static function get($sid)
    {
        if (empty($sid)) {
            throw new \InvalidArgumentException('$sid');
        }

        $key = static::_key($sid);
        $result = \Yii::$app->cache->get($key);
        return $result != false ? $result : null;
    }

    private static function _key($sid)
    {
        $key = sha1('passport:sso:' . $sid);
        return $key;
    }


}