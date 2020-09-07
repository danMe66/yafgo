<?php

/**
 * 业务类异常
 * Class Container_Exception_BusinessException
 */
class Container_Exception_BusinessException extends Container_Exception_BaseException
{
    /**
     * BusinessException constructor.
     * @param string $message 错误异常信息
     * @param int $code 错误异常编号
     * @param string $information 调试信息
     */
    function __construct($message, $code, $information = '')
    {
        parent::__construct($message, $code, $information);
    }

}