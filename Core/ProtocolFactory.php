<?php

use Thrift\Protocol\TCompactProtocol;

/**
 * 微服务协议相关
 * Class Container_Core_ProtocolFactory
 */
class Container_Core_ProtocolFactory
{
    /**
     * 获取服务协议
     * @param TCompactProtocol $protocol 协议
     * @param string $serviceName 服务名
     * @return \Thrift\Protocol\TMultiplexedProtocol
     */
    public static function getServiceProtocol($protocol, $serviceName)
    {
        return (new \Thrift\Protocol\TMultiplexedProtocol($protocol, $serviceName));
    }
}