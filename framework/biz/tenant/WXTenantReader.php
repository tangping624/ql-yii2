<?php

namespace app\framework\biz\tenant;

use yii\base\NotSupportedException;
use yii\web\Cookie;

class WXTenantReader implements TenantReaderInterface
{
    const OPENID_QUERY_STRING_KEY = 'openid';
    const PUBLIC_QUERY_STRING_KEY = 'public_id';

    protected static $openid = null;

    /**
     * @inheritdoc
     */
    public function getCurrentTenantCode()
    {
        $tenantCode = isset($_GET[static::TENANT_QUERY_STRING_KEY]) ? $_GET[static::TENANT_QUERY_STRING_KEY] : '';
        $tenantCode = $tenantCode == '' ? (isset($_POST[static::TENANT_QUERY_STRING_KEY]) ? $_POST[static::TENANT_QUERY_STRING_KEY] : '') : $tenantCode;
        return $tenantCode;
    }

    /**
     * @inheritdoc
     */
    public function getCurrentOrganizationId()
    {
        throw new NotSupportedException('getCurrentOrganizationId');
    }

    /**
     * @inheritdoc
     */
    public function getOpenId($openid='')
    {
        $publicId = $this->getPublicId();
        if (empty($publicId)) {
            throw new \yii\web\HttpException(403, '公众号id不能为空');
        }

        $cookieName = $publicId . '_openid';

        //get openid
        if ($openid == '') {
            if (static::$openid != null) {
                return static::$openid;
            }
            
            //测试和开发环境中支持url直接设置openid
            if (defined('WEIXIN_AGENT') && WEIXIN_AGENT == false) {
                $openid = isset($_GET[static::OPENID_QUERY_STRING_KEY]) ? $_GET[static::OPENID_QUERY_STRING_KEY] : '';
                if (!empty($openid)) {
                    $cookie = new Cookie();
                    $cookie->name = $cookieName;
                    $cookie->value = $openid;
                    \Yii::$app->response->cookies->add($cookie);
                }
            }

            if (empty($openid)) {
                //get openid from cookie
                $cookie = \Yii::$app->request->cookies->get($cookieName);
                if (isset($cookie)) {
                    $openid = $cookie->value;
                }
            }

        } else {
            $cookie = new Cookie();
            $cookie->name = $cookieName;
            $cookie->value = $openid;
            \Yii::$app->response->cookies->add($cookie);
        }

        static::$openid = $openid;
        return $openid;
    }

    /**
     * @inheritdoc
     */
    public function getPublicId()
    {
        $publicId = isset($_GET[static::PUBLIC_QUERY_STRING_KEY]) ? $_GET[static::PUBLIC_QUERY_STRING_KEY] : '';
        if(!empty($publicId)){
            $cookie = new Cookie();
            $cookie->name = 'public_id';
            $cookie->value = $publicId;
            \Yii::$app->response->cookies->add($cookie);
        }else{
            $cookie = \Yii::$app->request->cookies['public_id'];
            if (isset($cookie)) {
                $publicId = $cookie->value;
            }
        }

        return $publicId;

    }

}