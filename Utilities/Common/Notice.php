<?php

class Container_Utilities_Common_Notice
{
    /**
     * 微信机器人发送消息
     * @param string $url 机器人地址
     * @param string $content 发送的消息体
     */
    public static function sendWxWebhook($url, $content)
    {
        $msg = array(
            "msgtype" => 'markdown',
            'markdown' => [
                'content' => $content
            ]
        );
        $data = json_encode($msg, JSON_UNESCAPED_UNICODE);
        Container_Utilities_Common_Http::Request($url, $data);
        //因为markdown语法不能@人员，所以在这里在此@人员
        $sendMsg = [
            "msgtype" => 'text',
            'text' => [
                'content' => null,
                'mentioned_list' => ["@all"],
            ]
        ];
        $sendInfo = json_encode($sendMsg, JSON_UNESCAPED_UNICODE);
        Container_Utilities_Common_Http::Request($url, $sendInfo);
    }
}