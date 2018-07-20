<?php

namespace app\framework\biz\cache\models;

use app\framework\cache\CacheObject;

class NonceCache extends CacheObject
{

    /**
     * @var string
     */
    public $user_id;

    /**
     * @var string 登录账号
     */
    public $account;

    /**
     * @var string 用户昵称/名字
     */
    public $displayName;


    /**
     * @var string 登录账号
     */
    public $mobile;

    /**
     * @var string 用户昵称
     */
    public $nickName;

    /**
     * @var 用户分组
     */
    public $userType;
    /**
     * 用户名
     */
    public $name;
    /**
     * 头像地址
     * @var headimg_url
     */
    public $headimg_url;
    
}