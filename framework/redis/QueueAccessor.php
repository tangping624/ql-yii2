<?php

namespace app\framework\redis;

use app\framework\redis\interfaces\QueueAccessorInterface;

abstract class QueueAccessor implements QueueAccessorInterface
{

    protected function keyPrefix()
    {
        return '';
    }

    public function enqueue($name, $data)
    {
        if(empty($name)){
            throw new \InvalidArgumentException('$name');
        }

        if (!isset($data)) {
            throw new \InvalidArgumentException('$data');
        }


        if (!is_string($data)) {
            $data = json_encode($data);
        }

        $key = $this->createKey($name);
        $client = RedisClientManager::create();

        try
        {
            $client->rPush($key, $data);
        }
        catch(RedisException $ex)
        {
            throw $ex;
        }
        finally
        {
            $client->close();
        }

    }

    public function dequeue($name, $total = 1)
    {
        if(empty($name)){
            throw new \InvalidArgumentException('$name');
        }

        $key = $this->createKey($name);
        $client = RedisClientManager::create();

        try
        {

        }
        catch(RedisException $ex)
        {
            throw $ex;
        }
        finally
        {
            $client->close();
        }

    }

    protected function createKey($queueName)
    {
        if(empty($this->keyPrefix())){
            return $queueName;
        }
        return $this->keyPrefix() . static::KEY_SEPARATOR . $queueName;
    }
}