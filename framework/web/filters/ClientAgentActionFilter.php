<?php

namespace app\framework\web\filters;

use yii\base\ActionFilter;
use app\framework\utils\RequestHelper;

/**
 * 客户端代理访问限制
 */
class ClientAgentActionFilter extends ActionFilter
{
    public function beforeAction($action)
    {
        if (!\Yii::$app->request->isAjax) {
            if(!RequestHelper::isWeixinAgent())
            {
                echo '<html><head><meta charset="utf-8"><title>禁止访问</title></head><body>请在微信端浏览</body></html>';
                return false;
            }
        }

        return parent::beforeAction($action);
    }

}

