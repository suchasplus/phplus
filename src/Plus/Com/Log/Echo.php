<?php
/**
 *
 */

class Plus_Com_Log_Echo extends Plus_Com_Log_Abstract
{
    protected function _handler($text)
    {
        echo $text;
    }
}