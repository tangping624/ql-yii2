<?php

namespace app\framework\redis\interfaces;

interface QueueAccessorInterface
{
    const KEY_SEPARATOR = ':';

    public function enqueue($name, $data);

    public function dequeue($name, $total = 1);
}