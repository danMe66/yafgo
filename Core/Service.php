<?php

/**
 * 微服务相关
 * Trait Container_Core_Service
 */
class Container_Core_Service
{
    use Container_Tool_HandlerHelp;

    protected $service;

    const PMS = 'com.ciwei.pms.thriftApi.PMSService';
    const CMSForumTypeService = 'com.ciwei.cms.thriftApi.CMSForumTypeService';
    const CMSForumPlatformService = 'com.ciwei.cms.thriftApi.CMSForumPlatformService';
    const CMSForumThreadService = 'com.ciwei.cms.thriftApi.CMSForumThreadService';
    const CMSThreadReplayService = 'com.ciwei.cms.thriftApi.CMSThreadReplayService';
    const CMSForumCollectionService = 'com.ciwei.cms.thriftApi.CMSForumCollectionService';
    const CMSForumPropertyService = 'com.ciwei.cms.thriftApi.CMSForumPropertyService';
    const CMSForumStatisticsService = 'com.ciwei.cms.thriftApi.CMSForumStatisticsService';
    const GMSService = 'com.ciwei.ebs.gms.thriftApi.GMSService';
    const ESLService = 'com.ciwei.ebs.esl.thriftApi.ESLService';
    const SKUService = 'com.ciwei.ebs.sku.thriftApi.SKUService';
    const SPUService = 'com.ciwei.ebs.spu.thriftApi.SPUService';
    const SPMService = 'com.ciwei.ebs.spm.thriftApi.SPMService';
    const PSMUserTerminalsService = 'com.ciwei.psm.thriftApi.PSMUserTerminalsService';
    const PSMPushConfigurationManagement = 'com.ciwei.psm.thriftApi.PSMPushConfigurationManagementService';
    const PSMTemplateManagementService = 'com.ciwei.psm.thriftApi.PSMTemplateManagementService';
    const PSMPushQueueManagementService = 'com.ciwei.psm.thriftApi.PSMPushQueueManagementService';
    const RCMTemplateService = 'com.ciwei.rcm.thriftApi.RCMTemplateService';

    /**
     * consul服务
     */
    public function setService()
    {
        if (!empty($this->service)) {
            $service = $this->service;
            $this->service = Container_Core_Consul::getInstance()->getService($service);
            if (empty($this->service)) {
                $this->_setApiError(Container_Error_ErrDesc_ErrorDto::SERVICE_NOT_AVAILABLE);
                return $this->getResult(Container_Error_ErrDesc_ErrorCode::API_ERROR);
            }
        }
    }

    /**
     * 获取服务地址
     * @param string $model 服务模块名
     * @param string $serviceName 服务方法
     * @return mixed
     * @throws Exception
     */
    public function getServices(string $model, string $serviceName)
    {
        return Container_Core_ClientFactory::GetService($model, $serviceName, true);
    }

    /**
     * 获取服务名
     * @param string $module 服务模块名
     * @param string $serviceName 服务方法
     * @return string
     */
    public static function getServiceName(string $module, string $serviceName)
    {

        if (strpos($module, '\\') !== false) {
            $module = str_replace('\\', '.', $module);
        }
        return 'com.ciwei.' . $module . '.thriftApi.' . $serviceName;
    }

    /**
     * TODO::旧代码，不知道干嘛的
     * 获取引入的服务
     * @param string $serviceName 服务文件名称
     * @param bool $isException 是否引入异常类
     * @param string $service 服务简称 类似于cms
     */
    public static function getService(string $serviceName, bool $isException = true, string $service = '')
    {
        $serviceFileName = $serviceName . '.php';
        $root = APP_PATH . '/application/library/lib';
        if ($isException) {
            require_once $root . '/Types.php';
        }
        self::searchFile($root, $serviceFileName, $service);
    }

    /**
     * 递归查找lib目录文件
     * @param string $dir 目录
     * @param string $fileName 查找的文件名
     * @param string $service 服务简称 类似于cms
     */
    public static function searchFile(string $dir, string $fileName, string $service)
    {
        $temp = scandir($dir);
        foreach ($temp as $v) {
            $nowFile = $dir . '/' . $v;
            if (is_dir($nowFile)) {
                if ($v == '.' || $v == '..') continue;
                self::searchFile($nowFile, $fileName, $service);
            } else {
                if ($service == '') {
                    if ($v == $fileName) require_once $nowFile;
                } else {
                    if (strpos($nowFile, $service) !== false && $v == $fileName) {
                        require_once $nowFile;
                        break;
                    }
                }
            }
        }
    }
}