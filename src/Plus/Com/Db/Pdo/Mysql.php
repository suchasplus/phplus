<?php
/**
 *
 */

class Plus_Com_Db_Pdo_Mysql extends Plus_Com_Db_Pdo_Abstract
{
    protected function _dsn()
    {
        return "mysql:host=" . $this->_config['host'] . ";port=" . $this->_config['port'] . ";dbname=" . $this->_config['database'];
    }
}