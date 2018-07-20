<?php
 
namespace app\framework\utils;

class MutexDoing
{
    private $_key;

    public function __construct($name)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('$name');
        }
        $this->_key = 'mutex_do:locking:' . sha1($name);
    }

    /**
     * @param callable $fn
     * @param int $secondTimeout
     * @return bool
     * @throws \Exception
     */
    public function execute($fn, $secondTimeout)
    {
        if (intval($secondTimeout) <= 0) {
            throw new \InvalidArgumentException('$timeout 必须大于0');
        }

        $sleeping = 0;
        while (1) {
            $result = \Yii::$app->cache->add($this->_key, 'locking', $secondTimeout + 10);
            if ($result) {
                try {
                    $fn();
                    return true;
                } catch (\Exception $ex) {
                    throw $ex;
                } finally {
                    \Yii::$app->cache->delete($this->_key);
                }
            } else {
                $sleeping += 1000000 * 0.1;
                if ($sleeping < $secondTimeout) {
                    usleep(1000000 * 0.1);
                } else {
                    break;
                }
            }
        }
        return false;
    }

}