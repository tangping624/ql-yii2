<?php
defined('ROOT_NAMESPACE') or define('ROOT_NAMESPACE', 'app');
spl_autoload_register(function ($className) {
        $ds = DIRECTORY_SEPARATOR;
        $dir = __DIR__ . $ds. '..';
        $className = str_replace('\\', $ds, $className);
        $className = str_replace(ROOT_NAMESPACE . $ds, '', $className);
        $file = "{$dir}{$ds}{$className}.php";
        if (is_readable($file)) {
            require_once $file;
        }
});

