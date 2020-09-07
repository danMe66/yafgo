<?php

/**
 * handler相关配置类
 * Class Container_Handler_HandlerConfig
 */
final class Container_Handler_HandlerConfig
{
    /**
     * @var bool 是否需要token认证
     */
    public $_needToken;

    /**
     * @var bool 是否需要进行method检查
     */
    public $_checkMethod;


    /**
     * @var bool 是否切换为数据库只读实例
     */
    public $_useReadOnlyDb;

    /**
     * @var bool 是否验证请求
     */
    public $_checkRequest;

    function __construct($needToken = false, $checkMethod = true, $checkRequest = true, $useReadOnlyDb = false)
    {
        $this->_needToken = $needToken;
        $this->_checkMethod = $checkMethod;
        $this->_checkRequest = $checkRequest;
        $this->_useReadOnlyDb = $useReadOnlyDb;
    }

}