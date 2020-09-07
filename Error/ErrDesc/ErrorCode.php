<?php

/**
 *
 * Class Container_Error_ErrDesc_ErrorCode
 */
class Container_Error_ErrDesc_ErrorCode
{
    const API_SUCCESS = 1;
    const API_ERROR = -1;

    const UNRECOGNIZED_DATA_FORMAT = 1001;
    const CHECK_CODE_ERROR = 1002;
    const PAGING_STEP_LENGTH_ERROR = 1003;
    const DB_ERROR = 1004;
    const MYSQL_ERROR = 1005;
    const MICROSERVICES_ERROR = 1006;
    const NO_TOKEN = 1007;
    const ERROR_TOKEN = 1008;
    const NO_USER_INFO = 1009;
    const ACCOUNT_PASSWORD_ERROR = 1010;
    const NOT_AUTHORISED = 1011;

    const DEF_MSG = '100001_系统错误';

    static $ErrorDesc = [
        Container_Error_ErrDesc_ErrorCode::API_SUCCESS => 'ok',
        Container_Error_ErrDesc_ErrorCode::API_ERROR => 'error',

        Container_Error_ErrDesc_ErrorCode::UNRECOGNIZED_DATA_FORMAT => '不可识别的数据格式',
        Container_Error_ErrDesc_ErrorCode::CHECK_CODE_ERROR => '验证码错误',
        Container_Error_ErrDesc_ErrorCode::PAGING_STEP_LENGTH_ERROR => '100011_分页步长错误(1-50)',
        Container_Error_ErrDesc_ErrorCode::DB_ERROR => '数据库错误',
        Container_Error_ErrDesc_ErrorCode::MYSQL_ERROR => '数据库连接失败',
        Container_Error_ErrDesc_ErrorCode::MICROSERVICES_ERROR => '微服务异常',
        Container_Error_ErrDesc_ErrorCode::NO_TOKEN => '没有token',
        Container_Error_ErrDesc_ErrorCode::ERROR_TOKEN => 'token验证未通过',
        Container_Error_ErrDesc_ErrorCode::NO_USER_INFO => '此用户不存在，请检查',
        Container_Error_ErrDesc_ErrorCode::ACCOUNT_PASSWORD_ERROR => '密码不正确，请检查',
        Container_Error_ErrDesc_ErrorCode::NOT_AUTHORISED => '未授权的用户',
    ];
}