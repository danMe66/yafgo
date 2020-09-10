<?php

/**
 * 获取配置相关
 * Trait Container_Core_Trait_Config
 */
trait Container_Core_Trait_Config
{
    /**
     * 获取全局application.ini的配置
     * @return mixed
     */
    public function getConfig()
    {
        global $_G;
        return $_G['config'];
    }
}