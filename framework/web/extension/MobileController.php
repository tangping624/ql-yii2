<?php

namespace app\framework\web\extension;

use Yii;
use app\framework\biz\cache\FanCacheManager;

/**
 * 移动站点使用
 * Class MobileController
 * @property string $openid This property is read-only.
 * @property string $fanId This property is read-only. 
 * @property string $publicId 公众号id This property is read-only.
 */
class MobileController extends Controller
{
    public $enableCsrfValidation = true;

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (!$this->validateUrl()) {
                return false;
            }
            return true;
        }
        return false;
    }

    private function getMemberIdByOpenid($openid)
    {
        $result = FanCacheManager::getFan($openid);
        return $result ? $result->memberId : '';
    }

    /**
     * @return bool
     * @throws \yii\web\HttpException
     */
    protected function validateUrl()
    {
        $publicId = $this->getPublicId();
        if (empty($publicId)) {
            throw new \yii\web\HttpException(403, '缺少公众号id');
        }

        /*
        $openId = $this->getOpenid();
        if (empty($openId)) {
            throw new \yii\web\HttpException(403, '缺少openid');
        }*/

        return true;
    }

    public function getOpenid()
    {
        return Yii::$app->context->openid;
    }

    public function getFanId()
    {
        return Yii::$app->context->fanId;
    }
    
    

    public function getPublicId()
    {
        return Yii::$app->context->publicId;
    }

    public function behaviors()
    {
        $filters = [];
        if(!defined('WEIXIN_AGENT') || WEIXIN_AGENT == true){
            $filters[] = ['class' => 'app\framework\web\filters\ClientAgentActionFilter'];
        }

        //$filters[] = ['class' => 'app\framework\web\filters\AutoAddFanFilter'];
        return $filters;
    }
}
