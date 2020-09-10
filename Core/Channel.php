<?php

/**
 * 渠道相关
 * Trait Container_Core_Channel
 */
class Container_Core_Channel
{
    /**
     * 获取渠道号
     * @return int
     */
    public static function getChannel()
    {
        if (isset($_SERVER['HTTP_CHANNELTYPE'])) {
            if (strtolower($_SERVER['HTTP_CHANNELTYPE']) == 'wxapp') {
                return \com\ciwei\common\thriftApi\UserTokenChannel::mini_program;
            }
        }
        if (isset($_SERVER['HTTP_VERSION']) and is_numeric($_SERVER['HTTP_VERSION'])) {
            return \com\ciwei\common\thriftApi\UserTokenChannel::app;
        }
        return \com\ciwei\common\thriftApi\UserTokenChannel::web;
    }

    /**
     * 获取渠道名称
     * @return string
     */
    public static function getChannelName()
    {
        if (isset($_SERVER['HTTP_CHANNELTYPE'])) {
            if (strtolower($_SERVER['HTTP_CHANNELTYPE']) == 'wxapp') {
                return "wxapp";
            }
        }
        if (isset($_SERVER['HTTP_VERSION']) and is_numeric($_SERVER['HTTP_VERSION'])) {
            return "app";
        }
        return "web";
    }
}