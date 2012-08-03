<?php
/**
 *
 */

class Plus_Com_Log_Null extends Plus_Com_Log_Abstract
{
    protected function _handler($text)
    {
        return;
    }
}