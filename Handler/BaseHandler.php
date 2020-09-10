<?php

/**
 * 所有controller的基类
 * 用于获取请求参数（ip，版本号，用户验证信息，浏览器信息......）
 * Class Container_Handler_BaseHandler
 */
abstract class Container_Handler_BaseHandler extends Container_Handler_HandlerContext
{
    use Container_Tool_HandlerHelp;

    protected $script = "";

    /**
     * @var array 过滤不需要登陆的控制器
     */
    protected $exclusion = [];

    /**
     * @var int 用户ID
     */
    protected $userId;

    /**
     * @var string 客户端IP
     */
    protected $_ip;

    /**
     * @var array 请求参数
     */
    public $_params;

    /**
     * @var string 接口请求方式
     */
    public $_method;

    /**
     * @var string 用户浏览器信息
     */
    protected $_user_agent;

    /**
     * @var string 接口版本号
     */
    protected $_version;

    /**
     * @var string 路由地址（API接口）
     */
    protected $_route;

    /**
     * @var string 用户token
     */
    protected $_token;

    /**
     * @var object 请求的浏览器信息
     */
    protected $_browseInfo;

    /**
     * @var object 请求体
     */
    protected $_http_request;

    /**
     * @var
     */
    protected $requestBody;

    /**
     * @var container_handler_HandlerConfig
     */
    protected $_config;

    /**
     * 获取配置
     * @return mixed
     */
    protected abstract function setConfig();

    /**
     * 默认初始化方法，如果不需要，可以删除掉这个方法
     * 如果这个方法被定义，那么在Controller被构造以后，Yaf会调用这个方法
     */
    public function init()
    {
        $this->_ip = $this->getIp();//获取客户端请求的IP
        $this->_method = $this->getMethod();//获取HTTP的请求方式
        $this->_route = $this->getRoute();//获取用户请求的路由地址
        $this->_params = $this->getParams();//获取请求的参数
        $this->_user_agent = $this->getUserAgent();//获取用户浏览器信息
        $this->_version = $this->getVersion();//获取接口版本号
        $this->_token = $this->getToken();//获取用户请求的token验证信息
        $this->_browseInfo = $this->getBrowseInfo();//获取用户浏览器信息
        $this->_http_request = $this->getRequest();//Yaf框架自身属性，获取当前的请求实例
        $this->_config = new container_handler_HandlerConfig();
        $this->setConfig();
        $this->checkAuth();
        $this->work();
    }

    /**
     * 初始化调用一些验证参数，比如：setRequestBody，setResponseBody，_config....
     * 检查接口访问权限(可在这里进行接口访问权限的检测、做网关接口过滤、黑白名单过滤)
     */
    public function checkAuth()
    {
        //检查接口是否需要token验证
        if ($this->_config->needToken) {
            //获取当前调用的方法
            $actionName = strtolower($this->getRequest()->getActionName());
            //判断是否在排除名单只外
            $list = $this->exclusion;
            foreach ($list as $item) {
                if (strtolower($item) === $actionName) return;
            }
            $list = Container_Core_Auth::encodeToken($this->_token);
            if (!is_array($list) or count($list) != 2) {
                $this->_setApiError(Container_Error_ErrDesc_ErrorDto::USER_NOT_LOGIN);
                return $this->getResult(Container_Error_ErrDesc_ErrorCode::API_ERROR);
            }
            $uid = isset($list[0]) ? $list[0] : 0;
            $pass = isset($list[1]) ? $list[1] : "";
            $this->userId = $uid;
            $tokens = Container_Core_Auth::getCacheToken($uid);
            if (is_array($tokens)) {
                foreach ($tokens as $key => $token) {
                    if ($pass == $token) return;
                }
            }
            $tokens = Container_Core_Auth::getDiskToken($uid);
            if (is_array($tokens)) {
                foreach ($tokens as $key => $token) {
                    if ($pass == $token) return;
                }
            }
        }
        //TODO::是否需要对接口请求进行安全验证，暴力请求和恶意攻击等
        if ($this->_config->checkRequest) {

        }
        //TODO::是否需要对接口请求方式进行限制(需要严格规范请求方式)
        if ($this->_config->checkMethod) {

        }
    }

    public function work()
    {
        //检查此类是否需要setRequestBody
        if (method_exists($this, 'setRequestBody')) {
            $jsonMap = new JsonMapper();
            $jsonMap->bEnforceMapType = false;
            $content = json_decode(json_encode($this->_params, JSON_FORCE_OBJECT));
            set_error_handler(array($this, 'setMyRecoverableError'));
            try {
                $this->requestBody = $jsonMap->map($content, $this->setRequestBody());
            } catch (InvalidArgumentException $e) {
                $this->_setApiError(Container_Error_ErrDesc_ErrorDto::PARAM_FORMAT_REQ_ERROR);
                return $this->getResult(Container_Error_ErrDesc_ErrorCode::API_ERROR);
            }
            //检查入参
            $checkResMsg = $this->requestBody->checkFieldValue();
            if ($checkResMsg != 'success') {
                $this->_setApiError($checkResMsg);
                return $this->getResult(Container_Error_ErrDesc_ErrorCode::API_ERROR);
            }
        }
    }

    /**
     * 获取客户端请求的IP
     * @return string
     */
    public function getIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * 获取客户端的连接web服务器的端口
     * @return string
     */
    public function getPort()
    {
        return $_SERVER['REMOTE_PORT'];
    }

    /**
     * 获取用户请求的路由地址
     * @return string
     */
    public function getRoute()
    {
        $url = $this->getRequestUrl();
        $arr = parse_url($url);
        return str_replace($_SERVER['HTTP_HOST'], '', $arr['path']);
    }

    /**
     * 获取完整的请求URL地址
     * @return string
     */
    public function getRequestUrl()
    {
        return $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * 获取请求的参数
     * @return array
     */
    public function getParams()
    {
        if ($_SERVER['REQUEST_METHOD'] == "GET") {
            $url = $this->getRequestUrl();
            $arr = parse_url($url);
            if (empty($arr['query'])) return null;
            $params = $this->convertUrlQuery($arr['query']);
        } elseif ($_SERVER['REQUEST_METHOD'] == "POST") {
            $params = $_POST;
        } else {
            $params = '';
        }
        return $params;
    }

    /**
     * 获取HTTP的请求方式
     * @return string
     */
    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * 获取用户浏览器信息
     * @return mixed
     */
    public function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * TODO::获取接口版本号（后续的版本参数请求可以放在header请求头里边）
     * @return string
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * 获取用户请求的token
     * @return string
     */
    public function getToken()
    {
        return empty($_SERVER['HTTP_AUTHORIZATION']) ? null : $_SERVER['HTTP_AUTHORIZATION'];
    }

    /**
     * 获取用户浏览器信息
     * @return string
     */
    public function getBrowseInfo()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }
}