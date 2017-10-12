<?php

namespace marvin255\bxmigrate;

/**
 * Autoloader class.
 */
class Autoloader
{
    /**
     * @param string
     */
    protected static $_path = null;

    /**
     * @param string $path
     *
     * @return bool
     */
    public static function register($path = null)
    {
        self::$_path = $path ? $path : dirname(__FILE__);

        return spl_autoload_register(array(__CLASS__, 'load'), true, true);
    }

    /**
     * @param string $class
     */
    public static function load($class)
    {
        $prefix = __NAMESPACE__ . '\\';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $relative_class = substr($class, $len);
        $file = self::$_path . '/' . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
}

Autoloader::register(dirname(__FILE__));
