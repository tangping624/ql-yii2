<?php

namespace app\modules\api\repositories;

use app\framework\utils\StringHelper;
use app\repositories\RepositoryBase;

class WeixinLogRepository extends RepositoryBase
{
    /**
     * insertTemplateMsgLog
     * @param array $templateMsgLogRowData
     * @return int
     * @throws \yii\db\Exception
     */
    public function insertTemplateMsgLog($templateMsgLogRowData)
    {
        if (!array_key_exists('id', $templateMsgLogRowData)
            || empty($templateMsgLogRowData['id'])
        ) {
            $templateMsgLogRowData['id'] = StringHelper::uuid();
        }

        return $this->tenantDb->createCommand()->insert('p_template_msg', $templateMsgLogRowData)->execute();
    }

    /**
     * 微信支付日志
     * @param $data
     * @return bool
     */
    public function insertPayLog($data)
    {
        if (!isset($data['id'])||!$data['id']) {
            $data['id'] = StringHelper::uuid();
        }

        $data['created_on'] = $data['modified_on'] = date('Y-m-d H:i:s');

        return $this->tenantDb->createCommand()->insert('p_wxpay_log', $data)->execute();
    }
}