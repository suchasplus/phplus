<?php
/**
 *
 */
class Plus_Com_Log
{
	public static function factory($config)
	{
	    extract($config);
        $class = 'Plus_Com_Log_' . ucfirst($adapter);
        return new $class($params);
	}
}