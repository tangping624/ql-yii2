<?php

namespace app\framework\redis\interfaces;

interface RedisConnectionInterface
{
    /**
     * @return array
     */
    public function getConnectOption();
}