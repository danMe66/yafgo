<?php

namespace Container\Utilities\Constant {
    interface HttpConstant
    {
        //运营后台HTTP最少请求时长
        const HTTP_DELAY_TIME_ADMIN = 3000;
        //用户前台HTTP最少请求时长
        const HTTP_DELAY_TIME_WEB = 3000;
        //HTTP请求成功
        const HTTP_STATUS_SUCCESS = 1;
        //HTTP请求错误
        const HTTP_STATUS_ERROR = 0;
    }
}
