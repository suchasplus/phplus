<?php
class HelloworldWidget extends Plus_Com_Widget
{
    /**
     * 初始化Widget数据
     *
     * @param string $text
     */
    protected function _init($text)
    {
        $this->view->text = $text;
    }
}