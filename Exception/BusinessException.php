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
    function __construct($message, $code, $information)
    {
        $new_message = array_filter(explode('_', $message, 2));
        if (!empty($new_message[0]) && !empty($new_message[1])) {
            $code = $new_message[0];
            $message = $new_message[1];
        }
        parent::__construct($message, $code, $information);
    }

}