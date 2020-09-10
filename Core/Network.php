<?php

/**
 * HTTP 相关
 * Class Container_Core_Network
 */
class Container_Core_Network
{
    //获取客户端的ip地址
    public static function GetClientIp()
    {
        if (isset($_SERVER["REMOTE_ADDR"]) AND $_SERVER["REMOTE_ADDR"]) {
            $ip = $_SERVER["REMOTE_ADDR"];
        } elseif (isset($_SERVER["HTTP_CLIENT_IP"]) AND !empty($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) AND !empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            $ip = "Unknown";
        }
        return $ip;
    }

    /*发送HTTP请求*/
    public static function HttpRequest($curlOptions, $curl_info = null)
    {
        /* 设置CURLOPT_RETURNTRANSFER为true */
        if (!isset($curlOptions[CURLOPT_RETURNTRANSFER]) || $curlOptions[CURLOPT_RETURNTRANSFER] == false) {
            $curlOptions[CURLOPT_RETURNTRANSFER] = true;
        }
        /* 初始化curl模块 */
        $curl = curl_init();
        /* 设置curl选项 */
        curl_setopt_array($curl, $curlOptions);
        /* 发送请求并获取响应信息 */
        $responseText = '';
        try {
            $responseText = curl_exec($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($http_code != 200) {
                $log = Log::getInstance(array('filename' => "net-api"));
                $log->Write("error", "status :" . $http_code . "\n url : " . $curlOptions[CURLOPT_URL]);
            }

            if (($errno = curl_errno($curl)) != CURLM_OK) {
                $errmsg = curl_error($curl);
                //throw new \Exception($errmsg, $errno);
                $responseText = false;
            }
        } catch (Exception $e) {
            //exceptionDisposeFunction($e);
            $responseText = false;
        }
        if ($curl_info != null) {
            $responseText = array(
                'responseText' => $responseText,
                'curl_info' => curl_getinfo($curl),
            );
        }
        /* 关闭curl模块 */
        curl_close($curl);
        /* 返回结果 */
        return $responseText;
    }


    public static function RequestData($url, $data = null, $isBulid = false)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            $data = ($isBulid) ? http_build_query($data) : $data;
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    public static function RawRequestData($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));
        $result = curl_exec($ch);
        curl_close($ch);
        $ret = json_decode($result, true);
        return $ret;
    }


}
