<?php

namespace Container\Utilities\Constant {
    interface FileConstant
    {
        //慢日志目录
        const SLOW_LOG_PATH = "/request/slowLog";
        //错误日志目录
        const ERR_LOG_PATH = "/request/errorLog";
    }
}
