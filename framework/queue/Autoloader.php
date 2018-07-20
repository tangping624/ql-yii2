<?php
 
namespace app\framework\queue;

class Autoloader
{
    /**
     * Registers Raven_Autoloader as an SPL autoloader.
     */
    public static function register()
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array('app\framework\queue\Autoloader', 'autoload'));
    }


    /**
     * Handles autoloading of classes.
     *
     * @param string $className A class name.
     */
    public static function autoload($className)
    {
        $className = str_replace("\\", "/", $className);
        $class = __DIR__ . "/lib/{$className}";
        $file = $class . ".php";
        require_once $file;
    }
}