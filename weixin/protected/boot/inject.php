<?php

namespace app\boot\di;

/*framework requires*/
\Yii::$container->setSingleton('app\framework\cache\interfaces\KeyPrefixGeneratorInterface',
    'app\framework\cache\MobileKeyPrefixGenerator');

\Yii::$container->setSingleton('app\framework\cache\interfaces\CacheProviderInterface',
    'app\framework\cache\YiiCacheProvider');

\Yii::$container->setSingleton('app\framework\biz\tenant\TenantReaderInterface',
    'app\framework\biz\tenant\WXTenantReader');
 

\Yii::$container->setSingleton('app\framework\weixin\log\StoreInterface', 'app\framework\weixin\log\DbStore');

/**
 * 短信服务
 */
\Yii::$container->setSingleton('app\framework\sms\interfaces\SmsServiceInterface', 'app\framework\sms\SmsService');
\Yii::$container->setSingleton('app\framework\sms\interfaces\SmsProviderInterface', 'app\framework\sms\HaoserviceSmsProvider');

//微信接口
\Yii::$container->set('app\framework\weixin\interfaces\IAccessTokenRepository', 'app\framework\weixin\DbAccessTokenRepository');
\Yii::$container->set('app\framework\weixin\interfaces\IAccessTokenHelper', 'app\framework\weixin\AccessTokenHelper');
\Yii::$container->set('app\framework\weixin\component\IComponentAccessTokenRepository', 'app\framework\weixin\component\ComponentAccessTokenRepository');

// 注册微信消息队列
\Yii::$container->set('app\framework\weixin\inform\IInformService', 'app\framework\weixin\inform\DbInformService');

//参数设置
\Yii::$container->set('app\framework\settings\interfaces\SettingsAccessorInterface', 'app\framework\settings\SettingsAccessor');
\Yii::$container->set('app\framework\settings\interfaces\SettingsProviderInterface', 'app\framework\settings\SettingsProvider');

///////////////////////
