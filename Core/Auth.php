<?php

/**
 * token权限相关
 * Class Container_Core_Auth
 */
class Container_Core_Auth
{
    public static $client;//获取服务client

    public function __construct()
    {
        $service = new Container_Core_Service();
        self::$client = $service->getServices("ums", "UMSUserTokenService");
    }

    /**
     * 生成token
     * @param object $ret 参数对象
     */
    public static function convertToken(&$ret)
    {
        if (is_object($ret)) {
            if (!empty($ret->id) and !empty($ret->token)) {
                $uid = $ret->id;
                $token = $ret->token;
                $ret->token = base64_encode($uid . ":" . $token);
            }
        }
    }

    /**
     * 解密token
     * @param string $token token
     * @return array
     */
    public static function encodeToken($token)
    {
        $str = base64_decode($token);
        return explode(":", $str);
    }

    /**
     * 刷新token
     * @param int $user_id 用户ID
     * @param int $userTokenChannel 用户token渠道
     * @return array
     */
    public static function refreshToken(int $user_id, int $userTokenChannel)
    {
        try {
            return self::$client->refreshToken($user_id, $userTokenChannel);
        } catch (\MicroserviceException $e) {
            return ["code" => $e->getCode(), "msg" => $e->msg];
        } catch (\Exception $e) {
            return ["code" => $e->getCode(), "msg" => $e->getMessage()];
        }

    }

    /**
     * 获取用户渠道token
     * @param int $user_id
     * @param int $userTokenChannel
     * @return array
     */
    public static function getUserChannelToken(int $user_id, int $userTokenChannel)
    {
        try {
            return self::$client->getUserChannelToken($user_id, $userTokenChannel);
        } catch (\MicroserviceException $e) {
            return ["code" => $e->getCode(), "msg" => $e->msg];
        } catch (\Exception $e) {
            return ["code" => $e->getCode(), "msg" => $e->getMessage()];
        }

    }

    /**
     * 获取用户渠道token列表
     * @param int $user_id
     * @return array
     */
    public static function getUserChannelTokenList($user_id)
    {
        try {
            return self::$client->getUserChannelTokenList($user_id);
        } catch (\MicroserviceException $e) {
            return ["code" => $e->getCode(), "msg" => $e->msg];
        } catch (\Exception $e) {
            return ["code" => $e->getCode(), "msg" => $e->getMessage()];
        }
    }

    /**
     * 根据用户token获取userId
     * @param string $token 用户token
     * @return string
     */
    public static function getUserId($token)
    {
        if (!empty($token)) {
            if (is_string($token)) {
                return self::encodeToken($token)[0];
            }
        } else {
            return null;
        }
    }

    /**
     * 根据userId获取用户token(可能有多个token)
     * @param int $user_id userId
     * @return array
     * @throws ReflectionException
     * @throws container_exception_BaseException
     */
    public static function getCacheToken($user_id)
    {
        $class = new ReflectionClass('\com\ciwei\common\thriftApi\UserTokenChannel');
        $arrConst = $class->getConstant();
        $redis = self::getRedis(true, 9);
        $result = [];
        if (is_array($arrConst)) {
            $arr = array_values($arrConst);
            foreach ($arr as $channel) {
                $key = "passport_token" . $user_id . "#" . $channel;
                $content = $redis->get($key);
                if (!empty($content)) {
                    $result[$key] = $content;
                }
            }
        }
        return $result;
    }

    /**
     * 根据userId获取用户token
     * TODO::为什么会有两处获取token呢？
     * @param $user_id
     * @return array
     */
    public static function getDiskToken($user_id)
    {
        $ret = [];
        $result = self::getUserChannelTokenList($user_id);
        if (isset($result['code'])) {
            return $ret;
        } else {
            foreach ($result as $item) {
                $channel = $item->channel;
                $token = $item->token;
                $key = "passport_token" . $user_id . "#" . $channel;
                $ret[$key] = $token;
            }
        }
        return $ret;
    }

}