<?php

namespace app\framework\weixin\proxy\fw;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 获取微信用户相关接口
 *
 * @author Chenxy
 */
use app\framework\weixin\proxy\ApiBase;
use app\framework\weixin\interfaces\IAccessTokenHelper;

class JsSdk extends ApiBase
{
    public function __construct(IAccessTokenHelper $accessTokenHelper)
    {
        parent::__construct($accessTokenHelper);
    }
   
    public function getTicket()
    {
        return $this->execute("https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi", "GET", "获取jssdk-ticket");
    }
}
