<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\api\services;

use app\modules\api\repositories\BizRepository;
use app\modules\wechat\repositories\DkfRepository;
use app\modules\api\services\BizService; 

/**
 * Description of WeixinHandlerHelper
 *
 * @author Chenxy
 */
class WeixinHandlerHelper {

    public function initInternalProperties($handler, $data) {
        if (array_key_exists('ToUserName', $data)) {
            $handler->_wechat = $data['ToUserName'];
        }
        if (array_key_exists('publicDbConn', $data)) {
            $handler->_dbConnect = $data['publicDbConn'];
        }

        $handler->_bizService = new BizService(new BizRepository($handler->_dbConnect), new DkfRepository($handler->_dbConnect), $handler->_wechat);  
    }

}
