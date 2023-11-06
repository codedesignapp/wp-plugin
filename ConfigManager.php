<?php
class ConfigManager
{
    private static $config = null;

    public static function get($key)
    {
        if (self::$config === null) {
            $environment = (defined('CODEDESIGN_PLUGIN_ENV') && CODEDESIGN_PLUGIN_ENV === 'development') ? 'development' : 'production';
            self::$config = include $environment === 'development' ? 'config.dev.php' : 'config.prod.php';
        }
        return isset(self::$config[$key]) ? self::$config[$key] : null;
    }
}
