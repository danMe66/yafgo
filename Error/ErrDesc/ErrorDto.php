<?php

/**
 * 业务异常错误返回说明
 * Class Container_Error_ErrDesc_ErrorDto
 */
class Container_Error_ErrDesc_ErrorDto
{
    const PARAM_FORMAT_REQ_ERROR = '200001_参数无效或者参数格式不规范';
    const PARAM_FORMAT_RES_ERROR = '200002_接口返回必须是一个数组';
    const USER_NOT_LOGIN = '200003_请先登录';

    const SERVICE_NOT_AVAILABLE = '300001_当前服务不可用';
}