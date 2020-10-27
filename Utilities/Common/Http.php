<?php

use Container\Utilities\Constant\FileConstant;
use Container\Utilities\Constant\HttpConstant;

class Container_Utilities_Common_Http
{
    /**
     * 获取上个页面请求的url
     * @return mixed|null
     */
    public static function getPreUrl()
    {
        if (!isset($_SERVER["HTTP_REFERER"]) OR empty($_SERVER["HTTP_REFERER"])) {
            return null;
        }
        $data = parse_url($_SERVER["HTTP_REFERER"]);
        if ($data['host'] != $_SERVER['HTTP_HOST']) {
            return null;
        }
        return $_SERVER["HTTP_REFERER"];
    }

    /**
     * 获取当前页面地址
     * @return string
     */
    public static function getCurrentUrl()
    {
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) AND $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    public static function getHttp()
    {
        return ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://' . $_SERVER['HTTP_HOST'];
    }

    /**
     * 获取HTTP请求的开始时间点（单位：微秒）
     * @return mixed
     */
    public static function startHttpTime()
    {
        return Yaf_Registry::get('startMilliSecond');
    }

    /**
     * 获取HTTP请求的当前时间点（单位：微秒）
     * @return float
     */
    public static function endHttpTime()
    {
        return Container_Utilities_Common_Time::getMillisecond();
    }

    /**
     * 获HTTP请求的时长并记录慢日志
     * @param $logPath
     * @param array $reportInfo 是否对慢日志进行报警（微信机器人）$reportInfo['isNotice','content']
     */
    public static function getHttpDuration($logPath, $reportInfo)
    {
        $HttpDuration = self::endHttpTime() - self::startHttpTime();
        if ($HttpDuration >= HttpConstant::HTTP_DELAY_TIME_ADMIN) {
            //获取当前请求的地址
            $requestUrl = Yaf_Registry::get('REQUEST_URI');
            file_put_contents($logPath . '/' . date("Y-m-d", time()) . '.log', date("Y-m-d H:i:s", time()) . " API: " . $requestUrl . " slow_time:" . ($HttpDuration) . "\n", FILE_APPEND);
            if ($reportInfo['isNotice'] == true) {
                $content = str_replace("HttpDuration", $HttpDuration, $reportInfo['content']);
                Container_Utilities_Common_Notice::sendWxWebhook($reportInfo['url'], $content);
            }
        }
    }

    /**
     * 记录错误异常日志
     * @param $logPath
     * @param $exceptionCodePath
     * @param $exceptionCode
     * @param $exceptionMessage
     * @param $exceptionInformation
     */
    public static function setHttpException($logPath, $exceptionCodePath, $exceptionCode, $exceptionMessage, $exceptionInformation)
    {
        $data = date("Y-m-d H:i:s", time()) . " error_code_path: {$exceptionCodePath}" . "error_code：{$exceptionCode}" . ",message：" . json_encode($exceptionMessage, JSON_UNESCAPED_UNICODE) . "\n";
        file_put_contents($logPath . '/' . date("Y-m-d", time() . '.log'), $data, FILE_APPEND);
    }

    /**
     * 原始的http请求
     * @param string $url 请求的URL地址
     * @param null $data
     * @return bool|string
     */
    public static function Request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}