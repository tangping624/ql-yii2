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

class User extends ApiBase
{
    public function __construct(IAccessTokenHelper $accessTokenHelper)
    {
        parent::__construct($accessTokenHelper);
    }
    
    /**
     * 根据openid获取用户信息
     * @param string $openid
     * @return {
                "subscribe": 1,
                "openid": "o6_bmjrPTlm6_2sgVt7hMZOPfL2M",
                "nickname": "Band",
                "sex": 1,
                "language": "zh_CN",
                "city": "广州",
                "province": "广东",
                "country": "中国",
                "headimgurl":    "http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0",
               "subscribe_time": 1382694957,
               "unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"
            }
     */
    public function info($openid)
    {
        $params =['openid' => $openid];
        $userInfo = $this->execute('https://api.weixin.qq.com/cgi-bin/user/info', 'GET', "通过openid获取用户信息", $params);
        return $userInfo;
    }

    /**
     * 批量获取用户信息，一次最多100个
     * @param $openIds 格式：['openid1','openid2',...]
     * @return mixed
     */
    public function batchGetInfo($openIds)
    {
        if (!is_array($openIds) || empty($openIds)) {
            return [];
        }

        $cnt = 0;
        $arr = [];
        foreach ($openIds as $openId) {
            if ($cnt++ > 100) {
                break;
            }

            $arr[] = ['openid'=>$openId];//不要加 'lang'=>'zh-CN'，否则拿到的是英文的
        }

        return $this->execute('https://api.weixin.qq.com/cgi-bin/user/info/batchget', 'POST', "批量获取用户信息", ['user_list'=>$arr]);
    }
    
    /**
     * 获取用户列表
     * @param string $nextOpenId 第一个拉取的OPENID，不填默认从头开始拉取
     * @return object {"total":2,"count":2,"data":{"openid":["","OPENID1","OPENID2"]},"next_openid":"NEXT_OPENID"}
     */
    public function get($nextOpenId = '')
    {
        $params =['next_openid' => $nextOpenId];
        $userList = $this->execute('https://api.weixin.qq.com/cgi-bin/user/get', 'GET', '获取用户列表', $params);
        return $userList;
    }
}
