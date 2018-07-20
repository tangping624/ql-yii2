<?php

namespace app\framework\cache;

use Yii;
use Memcached;
use yii\caching\FileCache;
use yii\caching\MemCache;

use app\framework\settings\SettingsAccessor;

class AliyunCache extends  MemCache
{

    /**
     * @var FileCache
     */
    private static $_fileCache;

    public function init()
    {
        if(is_null(static::$_fileCache )){
            static::$_fileCache = new FileCache();
        }
        $key = defined(YII_ENV) ? "{YII_ENV}:ocs_config" : 'ocs_config';
        $config = static::$_fileCache->get($key);
        if($config == false){
            $settingsAccessor = new SettingsAccessor();
            $config = $settingsAccessor->get($key, false);
            $config = json_decode($config);
            static::$_fileCache->set($key, $config, 90000);
        }

        $servers = [];
        $servers[] = [
            'host' => $config->host,
            'port' => $config->port
        ];

        if(empty($config->host)){
            throw new \Exception('没有获取到OCS的连接地址配置值');
        }

        if(!empty($config->username)){
            $this->username = $config->username;
        }
        if(!empty($config->password)){
            $this->password = $config->password;
        }
        if(!empty($config->persistentId)){
            $this->persistentId = $config->persistentId;
        }

        $this->setServers($servers);
        $this->useMemcached = true;
        $this->options[Memcached::OPT_COMPRESSION] = false;
        $this->options[Memcached::OPT_BINARY_PROTOCOL] = true;

        parent::init();

    }

}