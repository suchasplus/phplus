<?php
/**
 *
 */

class Plus_Helper
{
    /**
     * Dynamic get helper
     *
     * @param string $key
     */
    public function __get($key)
    {
        $className = 'Plus_Helper_' . ucfirst($key);
        if (Plus::loadClass($className)) {
            return $this->$key = new $className();
        } else {
            throw new Exception("No helper like $key defined");
        }
    }
}