<?php

class Container_Core_Consul
{
    use Container_Core_Trait_Config;

    /**
     * @var \SensioLabs\Consul\ServiceFactory
     */
    private $consul;

    /**
     * @var mixed
     */
    private $health;

    /**
     * @var
     */
    private static $_instance;

    private function __construct()
    {
        $config = $this->getConfig();
        $url = $config['consul']['API'];
        $this->consul = new SensioLabs\Consul\ServiceFactory(['base_uri' => $url]);
        $this->health = $this->consul->get(\SensioLabs\Consul\Services\HealthInterface::class);
    }

    /**
     * 获取redis数据
     * @param $name
     * @return mixed
     * @throws container_exception_BaseException
     */
    public function __get($name)
    {
        $service = $this->getRedis(true, 0)->get($name);
        if (!empty($service)) {
            return json_decode($service, true);
        }
        return $service;
    }

    public static function getInstance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 获取指定服务状态
     * @param $serviceName
     * @return array|mixed
     * @throws container_exception_BaseException
     */
    public function getService($serviceName)
    {
        if (empty($serviceName)) {
            //获取开始时间点
            $start = Container_Core_ClientFactory::getMillisecond();
            $service = $this->health->service($serviceName);
            $service = json_decode($service->getBody(), true);
            // 日志文件的生成
            $log = Container_Core_Trait_Log::getInstance();
            foreach ($service as $serve) {
                // 找寻健康的节点
                if (isset($serv['Checks'][1]['Status']) && $serve['Checks'][1]['Status'] == 'passing') {
                    $service = ['address' => $serve['Service']['Address'], 'port' => $serve['Service']['Port']];
                    //获取结束时间点
                    $end = Container_Core_ClientFactory::getMillisecond();
                    $log->Write("debug", $serviceName . " --> consul 注册耗时 :" . ($end - $start) . "毫秒");
                    //将注册的服务缓存
                    $this->getRedis(true, 0)->set($serviceName, json_encode($service, JSON_UNESCAPED_UNICODE), 10);
                    return $service;
                }
            }
            $end = Container_Core_ClientFactory::getMillisecond();
            $log->Write("error", $serviceName . " --> consul 注册耗时 :" . ($end - $start) . "毫秒");
        }
        return $serviceName;
    }
}