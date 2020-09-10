<?php

/**
 * 业务异常错误返回说明
 * Class Container_Error_ErrDesc_ErrorDto
 */
class Container_Error_ErrDesc_ErrorDto
{
    /* 参数错误：10001-19999 */
    const PARAM_FORMAT_REQ_ERROR = '10001_参数无效或者参数格式不规范';
    const PARAM_TYPE_BIND_ERROR = '10002_参数类型错误';
    const RETURN_PARAM_TYPE_IS_ARRAY = '10003_接口返回必须是一个数组';
    const PARAM_NOT_COMPLETE = '10004_参数缺失';

    /* 用户错误：20001-29999*/
    const USER_NOT_LOGGED_IN = '20001_用户未登录';
    const USER_LOGIN_ERROR = '20002_账号不存在或密码错误';
    const USER_ACCOUNT_FORBIDDEN = '20003_账号已被禁用';
    const USER_NOT_EXIST = '20004_用户不存在';
    const USER_HAS_EXISTED = '20005_用户已存在';

    /* 微服务错误：30001-39999*/
    const SERVICE_NOT_AVAILABLE = '30001_当前服务不可用';

    /* 系统错误：40001-49999 */
    const SYSTEM_INNER_ERROR = '40001_系统繁忙，请稍后重试';

    /* 数据错误：50001-59999 */
    const RESULE_DATA_NONE = '50001_数据未找到';
    const DATA_IS_WRONG = '50002_数据有误';
    const DATA_ALREADY_EXISTED = '50003_数据已存在';

    /* 接口错误：60001-69999 */
    const INTERFACE_INNER_INVOKE_ERROR = '60001_内部系统接口调用异常';
    const INTERFACE_OUTTER_INVOKE_ERROR = '60002_外部系统接口调用异常';
    const INTERFACE_FORBID_VISIT = '60003_该接口禁止访问';
    const INTERFACE_ADDRESS_INVALID = '60004_接口地址无效';
    const INTERFACE_REQUEST_TIMEOUT = '60005_接口请求超时';
    const INTERFACE_EXCEED_LOAD = '60006_接口负载过高';

    /* 权限错误：70001-79999 */
    const PERMISSION_NO_ACCESS = '70001_无访问权限';


}