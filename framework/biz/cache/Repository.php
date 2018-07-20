<?php

namespace app\framework\biz\cache;

use yii\db\Query;
use app\framework\db\EntityBase; 

class Repository
{
 

    /**
     * @param string $dbId
     * @param string $tenantCode
     * @return array|bool
     */
    public function getDB()
    { 
        return EntityBase::getDb();
    }

    /**
     * @param $openId
     * @param $tenantCode
     * @return array|bool
     */
    public function getFanByOpenId($openId)
    {
        $db = EntityBase::getDb();
        $query = new \yii\db\Query();
        $cmd = $query->from('p_fan')
            ->where('openid=:openid and p_fan.is_deleted=0', [':openid' => $openId])
            ->select(' member_id, id, sex, nick_name, headimg_url,is_followed')
            ->createCommand($db);
        $dataRow = $cmd->queryOne();

        if ($dataRow != false && !is_null($dataRow['member_id'])) {
            $query = new \yii\db\Query();
            $cmd = $query->from('h_member')
                ->where(['id' => $dataRow['member_id'], 'is_deleted' => 0])
                ->select('name, sex')
                ->createCommand($db);

            $member_arr = $cmd->queryOne();
            if ($member_arr != false) {
                $dataRow['sex'] = $member_arr['sex'];
                $dataRow['name'] = $member_arr['name'];
            }else{
                $dataRow['sex'] = '';
                $dataRow['name'] = ''; 
                $dataRow['member_id'] = '';
            }
        }
        return $dataRow;
    }
 

    /**
     * @param $siteCode
     * @return false | string
     * @throws \Exception
     */
    public function getSiteUrl($siteCode)
    {
        $query = new Query();
        $cmd = $query->from('site')
            ->where('site_code=:site_code and is_deleted = 0', [':site_code' => $siteCode])
            ->select('url')
            ->createCommand();

        return $cmd->queryScalar();
    }
}
