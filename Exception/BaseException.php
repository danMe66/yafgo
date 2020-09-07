<?php

/**
 * 基础异常类，所有业务的异常均需要继承于此.除此异常外的异常均为系统级的异常
 * Class Container_Exception_BaseException
 */
class Container_Exception_BaseException extends \Exception
{

    /**
     * @var string 用于错误描述的调试性信息
     */
    public $extraInformation;

    /**
     * exception constructor.
     * @param string|array $message 错误异常信息
     */
    function __construct($message)
    {
        $this->message = $message;
        $this->code = -1;
        $this->extraInformation = '';
    }
}