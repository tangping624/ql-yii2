<?php 
namespace app\framework\utils;

/**
 *
 * 并发控制原子操作
 * $memcache = \Yii::$app->cache;
 * $mutex = new MutexLocker($memcache);
 * $business = 'vmember:activity:share';
 * $result = $mutex->lock($business, 3);
 * if($result) {
 *    //do something
 *    //$mutex->release();
 * } else {
 *   it's busy!
 * }
 */
class MutexLocker
{
    /**
     * @var \yii\caching\Memcache
     */
    protected $cache;

    public function __construct($cache)
    {
        $this->$cache = $cache;
    }

    public function lock($businessKey, $timeout=1)
    {
        $key = "locking:$businessKey";
        $lockVal = mt_rand(1, 2^31);
        $gotLock = $this->cache->add($key, $lockVal, 3600);
        if($gotLock) {
            $gotLock = $this->cache->get($key);
            return $gotLock === $lockVal;
        }
        $sleepTime = .01;
        $sleepTimeIncrement = .05;
        while(1) {
            usleep($sleepTime * 1000000);
            $sleepTime += $sleepTimeIncrement;
            if($sleepTime > $timeout) $sleepTime = $timeout;
            $exists = $this->cache->get($key);
            if(!$exists) break;
        }
        return false;
    }


    function release($businessKey)
    {
        $key = "locking::$businessKey";
        return ($this->cache->delete($key));
    }
}