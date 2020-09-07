<?php

/**
 * 参数校验验证错误返回
 * Class Container_Error_AssertProxy_ErrorDto
 */
class Container_Error_AssertProxy_ErrorDto
{
    const PARAM_ERROR = '100001_:s不能为空或类型错误';
    const PARAM_TYPE_ERROR = '100002_:s类型错误';
    const PARAM_OUT_OF_RANGE = '100003_:s取值超出限定范围';
    const VALUE_ERROR = '100004_取值错误必须&gt;0';
    const PARAM_ILLEGAL = '100005_:s非法参数';
    const VALUE_NOT_EMPTY = '100006_:s参数值不能为空';
    const VALUE_FORMAT_ERROR = '100007_:s取值格式错误';
}