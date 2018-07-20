<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\framework\biz\tenant;
use app\framework\db\EntityBase; 
class TenantHelper
{
    /**
     * 是否集团绑定公众号模式
     * @param string $tenantCode
     * @return bool
     */
    public static function isGroupBindWechatAccount($tenantCode = '')
    { 
        $dbConn = EntityBase::getDb(); 
        // 判断集团是否绑定公众号
        $sql = "select id from p_account where corp_id = '11b11db4-e907-4f1f-8835-b9daab6e1f23' and is_deleted=0";
        $row = $dbConn->createCommand($sql)->queryOne();
        
        // 集团绑定则为集团模式，否则为公司模式
        return $row ? true : false;
    }
}
