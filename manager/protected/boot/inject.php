<?php

namespace app\boot\di;

/*framework requires*/
\Yii::$container->set('app\framework\cache\interfaces\KeyPrefixGeneratorInterface',
    'app\framework\cache\KeyPrefixGenerator');

\Yii::$container->set('app\framework\cache\interfaces\CacheProviderInterface',
    'app\framework\cache\YiiCacheProvider');

\Yii::$container->set('app\framework\biz\tenant\TenantReaderInterface',
    'app\framework\biz\tenant\TenantReader');

/**
 * 短信服务
 */
\Yii::$container->setSingleton('app\framework\sms\interfaces\SmsServiceInterface', 'app\framework\sms\SmsService');
\Yii::$container->setSingleton('app\framework\sms\interfaces\SmsProviderInterface', 'app\framework\sms\HaoserviceSmsProvider');

//参数设置
\Yii::$container->set('app\framework\settings\interfaces\SettingsAccessorInterface', 'app\framework\settings\SettingsAccessor');
\Yii::$container->set('app\framework\settings\interfaces\SettingsProviderInterface', 'app\framework\settings\SettingsProvider');

//登录session , token
\Yii::$container->set('app\framework\auth\interfaces\TokenAccessorInterface', 'app\framework\auth\CookieTokenAccessor');
\Yii::$container->set('app\framework\auth\interfaces\AuthorizationInterface', 'app\framework\auth\Authorization');
\Yii::$container->set('app\framework\auth\interfaces\UserSessionAccessorInterface', 'app\framework\auth\UserSessionAccessor');

//微信接口
\Yii::$container->set('app\framework\weixin\interfaces\IAccessTokenRepository', 'app\framework\weixin\DbAccessTokenRepository');
\Yii::$container->set('app\framework\weixin\component\IComponentAccessTokenRepository', 'app\framework\weixin\component\ComponentAccessTokenRepository');
\Yii::$container->set('app\framework\weixin\interfaces\IMsgTemplateRepository', 'app\framework\weixin\msgtemplate\DbMsgTemplateRepository');

//微信日志
\Yii::$container->setSingleton('app\framework\weixin\log\StoreInterface', 'app\framework\weixin\log\DbStore');

//微信客服消息通知
\Yii::$container->set('app\framework\weixin\inform\IInformService', 'app\framework\weixin\inform\DbInformService');

///////////////////////
