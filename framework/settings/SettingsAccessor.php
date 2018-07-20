<?php
namespace app\framework\settings;

use app\framework\cache\CachePackageManger;
use app\framework\settings\interfaces\SettingsAccessorInterface;
use app\framework\settings\interfaces\SettingsProviderInterface;

class SettingsAccessor implements SettingsAccessorInterface
{
    /**
     * @var SettingsProviderInterface
     */
    protected $settingsProvider;
    private static $_cacheArr = [];

    public function __construct()
    {
        $this->settingsProvider = \Yii::$container->get('app\framework\settings\interfaces\SettingsProviderInterface');
    }

    /**
     * @inheritdoc
     */
    public function get($key, $useCache=true)
    {
        if (empty($key))
        {
            return null;
        }
        else
        {
            if(array_key_exists($key, static::$_cacheArr)){
                return static::$_cacheArr[$key];
            }

            $value = null;
            if($useCache)
            {
                $value = CachePackageManger::instance('settings:' . $key, 0)->get();
                if(!isset($value))
                {
                    $dataRow = $this->settingsProvider->get($key);
                    if (isset($dataRow)) {
                        $this->set($dataRow['name'], $dataRow['value'], $dataRow['description']);
                        $value = $dataRow['value'];
                    }else{
                        throw new \Exception('缺少配置项'. $key);
                    }
                }
            }
            else
            {
                $dataRow = $this->settingsProvider->get($key);
                $value = isset($dataRow) ? $dataRow['value'] : null;
            }
            static::$_cacheArr[$key] = $value;
            return $value;
        }
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value, $description)
    {
        if (empty($key)) {
            throw new \InvalidArgumentException('$key');
        }
        if ($value == null) {
            throw new \InvalidArgumentException($key.'的$value不能为null');
        }
        if (empty($description)) {
            throw new \InvalidArgumentException('$description');
        }

        $result = $this->settingsProvider->upset($key, $value, $description);
        if ($result) {
            CachePackageManger::instance('settings:' . $key, 0)->set($value, 3600);
            return true;
        }
        return false;
    }
}
