<?php

namespace app\modules\api\repositories;

use yii\db\Query;
use app\modules\RepositoryBase;
use app\framework\utils\StringHelper;
use app\entities\PAccount;
class PublicAccountRepository extends RepositoryBase
{
    /**
     * @param string $accountId
     * @param array $columns
     * @return array|false
     */
    public function getMch($accountId, $columns)
    {
        if (!is_array($columns)) {
            throw new \InvalidArgumentException('$columns');
        }
        if (count($columns) < 1) {
            throw new \InvalidArgumentException('$columns 至少要有一个元素');
        }

        if (empty($accountId)) {
            return false;
        }

        $query = new Query();
        $row = $query->from('p_account')
            ->where('id=:id and is_deleted=0')
            ->select($columns)
            ->createCommand(PAccount::getDb())
            ->bindValue(':id', $accountId)
            ->queryOne();

        if (in_array('mch_key', $columns) && !$row['mch_key']) {
            //如果没有则随机生成一个
            $mchkey = md5(StringHelper::uuid());
             PAccount::getDb()->createCommand()->update('p_account', ['mch_key'=>$mchkey], 'id=:id', [':id'=>$accountId])->execute();
            $row['mch_key'] = $mchkey;
        }

        return $row;
    } 
}
