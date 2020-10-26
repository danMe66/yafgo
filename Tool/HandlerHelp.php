<?php

use Container\Utilities\Constant\DomainConstant;
use Container\Utilities\Constant\FileConstant;
use Container\Utilities\Constant\NoticeConstant;

trait Container_Tool_HandlerHelp
{
    /**
     * @var array 返回结果集
     */
    protected $_result;

    /**
     * 将字符串参数变为数组
     * @param string $query 字符串
     * Example：['a'=>12,'b'=>123]
     * @return array
     */
    public function convertUrlQuery(string $query)
    {
        $queryParts = explode('&', $query);
        $params = [];
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        return $params;
    }

    /**
     * 将数组参数变为字符串
     * Example:"a=12&b=123"
     * @param array $array_query 数组参数
     * @return string
     */
    public function getUrlQuery(array $array_query)
    {
        $tmp = [];
        foreach ($array_query as $k => $param) {
            $tmp[] = $k . '=' . $param;
        }
        return implode('&', $tmp);
    }

    /**
     * 获取返回参数
     * @param $code
     * @return string
     */
    public function getResult($code)
    {
        return $this->_return($code);
    }

    /**
     * API 返回
     * @param int $code 状态码
     * @return false|string
     */
    protected function _return($code)
    {
        $response = [
            'code' => $code,
            'msg' => $this->_result['desc'],
            'data' => (object)$this->_result['data']
        ];;
//        header('Content-Type:application/json');//加上这行,前端那边就不需要var result = $.parseJSON(data);
        //记录请求日志
        $content = "# **ALD项目API超时告警⚠️**\n" .
            "> 所属项目：<font color=\"info\">ALD运营后台</font> \n" .
            "> API地址：<font color=\"info\">" . Yaf_Registry::get('REQUEST_URI') . "</font> \n" .
            "> 耗时：<font color=\"info\">HttpDuration</font> \n";
        $reportInfo = [
            'isNotice' => NoticeConstant::IS_NOTICE_TRUE,
            'url' => DomainConstant::WECHAT_MONITOR,
            'content' => $content,
        ];
        $slowLogPath = $GLOBALS['_G']['config']['log']["path"] . FileConstant::SLOW_LOG_PATH;
        Container_Utilities_Common_Http::getHttpDuration($slowLogPath, $reportInfo);
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * 设置API接口错误返回数据
     * @param string $errorCode
     */
    protected function _setApiError(string $errorCode)
    {
        $returnData = array_filter(explode('_', $errorCode, 2));
        if (!empty($returnData[0]) && !empty($returnData[1])) {
            $this->_result['data'] = (object)[];
            $this->_result['desc'] = $returnData[1];
        } else {
            $this->_result['data'] = (object)[];
            $this->_result['desc'] = $errorCode;
        }
    }

    /**
     * 设置API接口成功返回数据
     * @param array $data
     * @return array|string
     */
    protected function _setApiSuccess(array $data)
    {
        if (is_array($data)) {
            $msg = empty($data['msg']) ? 'success' : $data['msg'];
            $this->_result['data'] = $data['data'];
            $this->_result['desc'] = $msg;
        } else {
            $this->_setApiError(Container_Error_ErrDesc_ErrorCode::$ErrorDesc[Container_Error_ErrDesc_ErrorCode::UNRECOGNIZED_DATA_FORMAT]);
            return $this->getResult(Container_Error_ErrDesc_ErrorCode::API_ERROR);
        }
    }

    /**
     * @param $code
     * @param $message
     * @param $file
     * @param $line
     */
    function setMyRecoverableError($code, $message, $file, $line)
    {
        if ($message === 'Object of class stdClass could not be converted to string') {
            $this->_setApiError('1002_参数无效');
            echo $this->_return();
            exit();
        }
    }
}