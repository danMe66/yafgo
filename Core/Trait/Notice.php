<?php

/**
 * 消息通知
 * Example：微信机器人、钉钉机器人、邮件、短信...
 * Trait Container_Core_Trait_Notice
 */
trait Container_Core_Trait_Notice
{
    /**
     * 机器人报警通知
     * @param string $user 要发送的机器人
     * @param string $msg 消息内容
     * @param string $type 消息类型
     * @return array
     */
    public function reportPolice($user, $msg, $type)
    {
        return [];
    }
}