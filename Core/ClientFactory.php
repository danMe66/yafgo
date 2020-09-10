<?php

/**
 * 协议工厂类
 * Class Container_Core_ClientFactory
 */
class Container_Core_ClientFactory
{
    private static $socket;
    private static $transport;
    private static $protocol;
    /**
     * @var array 过滤不需要打印的日志的微服务
     */
    private static $exclusionModule = ["psm"];
    /**
     * @var array 过滤掉不需要打印日志的微服务名
     */
    private static $exclusionServiceName = [];

    /**
     * 获取协议
     * @param $host
     * @param $port
     */
    private static function getProtocol($host, $port)
    {
        self::$socket = new \Thrift\Transport\TSocket($host, $port);
        self::$transport = new \Thrift\Transport\TFramedTransport(self::$socket);
        self::$protocol = new \Thrift\Protocol\TCompactProtocol(self::$transport);
    }

    /**
     * 获取cms类型服务客户端
     * @param array $service
     * @return \com\ciwei\cms\thriftApi\CMSForumThreadServiceClient
     */
    public static function getCMSForumThreadServiceClient(array $service)
    {
        self::getProtocol($service['address'], $service['port']);
        $cmsProtocol = Container_Core_ProtocolFactory::getServiceProtocol(self::$protocol, Container_Core_Service::CMSForumThreadService);
        self::$transport->open();
        Container_Core_Service::getService('CMSForumThreadService');
        Container_Core_Service::getService('Types');
        return new \com\ciwei\cms\thriftApi\CMSForumThreadServiceClient($cmsProtocol);
    }


    /**
     * spm客户端
     * @param array $service
     * @return \com\ciwei\ebs\spu\thriftApi\SPMServiceClient
     */
    public static function getSPMServiceClient(array $service)
    {
        self::getProtocol($service['address'], $service['port']);
        $cmsProtocol = Container_Core_ProtocolFactory::getServiceProtocol(self::$protocol, Container_Core_Service::SPMService);
        self::$transport->open();
        Container_Core_Service::getService('SPMService');
        Container_Core_Service::getService('Types');
        return new \com\ciwei\ebs\spu\thriftApi\SPMServiceClient($cmsProtocol);
    }

    /**
     * @param array $service
     * @param bool $isClient
     * @param string $functionName
     * @param array $params
     * @return \com\ciwei\psm\thriftApi\PSMPushQueueManagementServiceClient|mixed
     */
    public static function getPSMPushQueueManagementService(array $service, bool $isClient = true, string $functionName = '', array $params = [])
    {
        self::getProtocol($service['address'], $service['port']);
        $cmsProtocol = Container_Core_ProtocolFactory::getServiceProtocol(self::$protocol, Container_Core_Service::PSMPushQueueManagementService);
        self::$transport->open();
        Container_Core_Service::getService('PSMPushQueueManagementService');
        Container_Core_Service::getService('Types');
        $client = new \com\ciwei\psm\thriftApi\PSMPushQueueManagementServiceClient($cmsProtocol);
        if ($isClient === true) {
            return $client;
        } else {
            return call_user_func_array([$client, $functionName], $params);
        }
    }

    /**
     * @param array $service
     * @param bool $isClient
     * @param string $functionName
     * @param array $params
     * @return \com\ciwei\rcm\thriftApi\RCMTemplateServiceClient|mixed
     */
    public static function getRCMTemplateService(array $service, bool $isClient = true, string $functionName = '', array $params = [])
    {
        self::getProtocol($service['address'], $service['port']);
        $cmsProtocol = Container_Core_ProtocolFactory::getServiceProtocol(self::$protocol, Container_Core_Service::RCMTemplateService);
        self::$transport->open();
        Container_Core_Service::getService('RCMTemplateService');
        Container_Core_Service::getService('Types');
        $client = new \com\ciwei\rcm\thriftApi\RCMTemplateServiceClient($cmsProtocol);
        if ($isClient === true) {
            return $client;
        } else {
            return call_user_func_array([$client, $functionName], $params);
        }
    }

    /**
     * @param string $module 模块
     * @param string $serviceName 服务名
     * @param bool $isClient 是否返回客户端
     * @param string $functionName 方法名
     * @param array $params 参数
     * @return mixed
     * @throws container_exception_BaseException
     */
    public static function GetService(string $module, string $serviceName, bool $isClient = true, string $functionName = '', array $params = [])
    {
        $consulService = Container_Core_Consul::getInstance()->getService($module);
        $start = self::getMillisecond();
        if (empty($consulService)) {
            throw new \Exception('当前' . $module . '不可用');
        }
        self::getProtocol($consulService['address'], $consulService['port']);
        $Protocol = Container_Core_ProtocolFactory::getServiceProtocol(self::$protocol, Container_Core_Service::getServiceName($module, $serviceName));
        self::$socket->setSendTimeout(3 * 1000 * 1000);
        self::$socket->setRecvTimeout(3 * 1000 * 1000);
        self::$transport->open();
        $className = '\com\ciwei\\' . $module . '\\thriftApi\\' . $serviceName . 'Client';
        $client = new $className($Protocol);
        if ($isClient === true) {
            return $client;
        } else {
            $data = call_user_func_array([$client, $functionName], $params);
            self::writeLog($module, $serviceName, $functionName, $start, $params);
            return $data;
        }
    }

    /**
     * 调用微服务 返回数组的格式
     * @param string $module
     * @param string $serviceName
     * @param bool $isclient
     * @param string $functionName
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public static function getArrayService(string $module, string $serviceName, bool $isclient = true, string $functionName = '', array $params = [])
    {
        $consulService = Container_Core_Consul::getInstance()->getService($module);
        $start = self::getMillisecond();
        if (empty($consulService)) {
            throw new \Exception('当前' . $module . '不可用');
        }
        self::getProtocol($consulService['address'], $consulService['port']);
        $Protocol = Container_Core_ProtocolFactory::getServiceProtocol(self::$protocol, Container_Core_Service::getServiceName($module, $serviceName));
        self::$socket->setSendTimeout(3 * 1000 * 1000);
        self::$socket->setRecvTimeout(3 * 1000 * 1000);
        self::$transport->open();
        $className = '\com\ciwei\\' . $module . '\\thriftApi\\' . $serviceName . 'Client';
        $client = new $className($Protocol);
        if ($isclient === true) {
            return $client;
        } else {
            $data = call_user_func_array([$client, $functionName], $params);
            // 对象更改为数组
            $data = json_decode(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), true);
            self::writeLog($module, $serviceName, $functionName, $start, $params);
            return $data;
        }
    }

    /**
     * 关闭socket连接
     */
    public static function close()
    {
        if (!empty(self::$transport)) {
            self::$transport->close();
        }
    }

    /**
     * 获取毫秒数
     * @return float
     */
    public static function getMillisecond()
    {
        list($m_sec, $sec) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($m_sec) + floatval($sec)) * 1000);
    }

    /**
     * 记录SGW 请求的日志
     * @param string $module 微服务
     * @param string $serviceName 类名
     * @param string $functionName 方法名
     * @param string $start 请求开始时间（ms）
     * @param array $params 请求的参数
     */
    private static function writeLog($module, $serviceName, $functionName, $start, $params)
    {
        $end = self::getMillisecond();
        // 日志文件的生成
        $log = Container_Core_Trait_Log::getInstance();
        $diffSecond = ($end - $start);
        // 排除部分日志的发送参数记录，比如PSM的发送
        if (in_array($module, self::$exclusionModule) or in_array($serviceName, self::$exclusionServiceName)) {
            $msg = $module . "--" . $serviceName . "--" . $functionName . "--耗时：" . $diffSecond . "毫秒";
        } else {
            $msg = $module . "--" . $serviceName . "--" . $functionName . "--params--" . json_encode($params, JSON_UNESCAPED_UNICODE) . "--耗时：" . $diffSecond . "毫秒";
        }
        if ($diffSecond >= 3000) {
            // 给程序员GG们发送警告,微服务调用报警
            $noticeData = array(
                "module" => $module,
                "serviceName" => $serviceName,
                "functionName" => $functionName,
                "diffSecond" => $diffSecond,
            );
            //TODO::发送警告(钉钉或者微信机器人消息通知)
//            Source_Shixi_Notice::sendProgrammaMonitorNotice($noticeData, 0, 0, 1);
            $log->Write("error", "微服务请求超过3秒:" . $msg);
        } elseif ($diffSecond >= 1000) {
            $log->Write("warning", "微服务请求超过1秒:" . $msg);
        } else {
            $log->Write("info", "微服务请求:" . $msg);
        }
    }
}