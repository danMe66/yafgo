<?php

/**
 * handler相关配置类
 * @property boolean needToken
 * @property boolean needCollection
 * @property boolean checkMethod
 * @property boolean checkRequest
 */
final class Container_Handler_HandlerConfig
{
    /**
     * @var bool 是否需要token认证
     */
    protected $_needToken;

    /**
     * @var bool 是否需要进行用户数据采集
     */
    protected $_needCollection;

    /**
     * @var bool 是否需要进行method检查
     */
    protected $_checkMethod;

    /**
     * @var bool 是否验证请求
     */
    protected $_checkRequest;

    function __construct($needToken = false, $needCollection = false, $checkMethod = false, $checkRequest = false)
    {
        $this->_needToken = $needToken;
        $this->_needCollection = $needCollection;
        $this->_checkMethod = $checkMethod;
        $this->_checkRequest = $checkRequest;
    }
}