<?php
namespace app\framework\auth;

use app\framework\db\EntityBase;

class UserSession
{

    public $key;

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

    public $mobile;
    /**
     *用户头像
     * @var headimg_url
     */
    public $headimg_url;

    /*
     * 会员ID*/
    public $memberId;


    /**
     * @var string 用户名称
     */
    public $name;


    public function toConnection()
    { 
        return EntityBase::toConnection();
    }

}
