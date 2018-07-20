<?php

namespace app\modules\api\repositories;

use app\modules\RepositoryBase;

class MemberRepository extends RepositoryBase
{
    /**
     * 获取会员的openid
     * @param  array $memberId
     * @return array
     */
    public function getOpenidList($memberId)
    {
        if (empty($memberId)) {
            return [];
        }

        $query = new \yii\db\Query();
        return $query->from('p_fan')
            ->where(['member_id' => $memberId, 'is_deleted' => 0])
            ->select('openid')
            ->createCommand($this->tenantDb)
            ->queryColumn();

    }

    /**
     * 获取粉丝的openid, id
     * @param  array $memberId
     * @param string $accountId
     * @return array
     */
    public function getOpenidAndFanIdList($memberId, $accountId='')
    {
        if (empty($memberId)) {
            return [];
        }

        $query = new \yii\db\Query();
        $query->from('p_fan')
            ->where(['member_id' => $memberId, 'is_deleted' => 0])
            ->select('openid, id');

        if (!empty($accountId)) {
            $query->andWhere('account_id=:account_id', [':account_id' => $accountId]);
        }

        return $query->createCommand($this->tenantDb)
            ->queryAll();
    }

}