<?php

namespace app\framework\auth\interfaces;


interface TokenAccessorInterface
{
    public function getToken();

    public function setToken($token);

    public function removeToken();

     
}
