<?php

/**
 * API请求的基类
 * Class Container_Base_ApiController
 */
abstract class Container_Base_ApiHandler extends Container_Handler_BaseHandler
{
    /**
     * 返回请求body要转化的实体请求类
     * @return mixed
     */
    abstract public function setRequestBody();

    /**
     * 返回响应的body要转化的实体请求类
     * @return mixed
     */
    abstract public function setResponseBody();

    /**
     * 配置参数
     * @return mixed
     */
    abstract public function _config();

    /**
     * 必填参数
     * @return array
     */
    abstract public function _requiredParams();

    /**
     * @return mixed
     */
    abstract public function run();
}