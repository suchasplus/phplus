<?php
/**
 *
 */

class Plus_Com
{
    public static function db($config)
    {
        return Plus_Com_Db::factory($config);
    }

    public static function cache($name = null)
    {
        if (is_array($name)) {
            return Plus_Com_Cache::factory($name);
        }

        if (empty($name)) {
            $name = '_cache';
        }

        if ($cache = Plus::reg($name)) return $cache;

        $config = (array)Plus::config($name);
        $cache = Plus_Com_Cache::factory($config);
        Plus::reg($name, $cache);
        return $cache;
    }

    public static function log($config)
    {
        return Plus_Com_Log::factory($config);
    }

    /**
     * Dynamic get Component
     *
     * @param string $key
     */
    public function __get($key)
    {
        $className = 'Plus_Com_' . ucfirst($key);
        if (Plus::loadClass($className)) {
            return $this->$key = new $className();
        } else {
            throw new Plus_Exception("No component like $key defined");
        }
    }
}