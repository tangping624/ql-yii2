<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 2015/5/5
 * Time: 16:12
 */
namespace app\models;

use Yii;
use yii\base\Model;
use app\entities\TApp;

class App extends Model
{
    public function searchApps($tenantCode)
    {
        $query = (new \yii\db\Query())
            ->select('application.*')
            ->distinct()
            ->from('tenant')
            ->innerJoin('contract', 'tenant.id=contract.`tenant_id`')
            ->innerJoin('authorization', 'contract.id=authorization.`contract_id`')
            ->innerJoin('application', 'authorization.`application_id` = application.id')
            ->where('application.is_deleted=0')
            ->andWhere('authorization.is_deleted=0')
            ->andWhere("FIND_IN_SET('服务号应用', application.type)>0")
            ->andWhere(['=', 'tenant.code', $tenantCode])
            ->orderBy('application.app_name ASC');

        $command = $query->createCommand(\yii::$app->db);
        $rows = $command->queryAll();
        return $rows;
    }
}
