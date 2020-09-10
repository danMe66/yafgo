<?php

/**
 * 获取redis，mysql实例
 * Trait Container_Handler_HandlerContext
 */
abstract class Container_Handler_HandlerContext extends Yaf_Controller_Abstract
{
    use Container_Core_Trait_Config;

    public $global_config;

    public $redis_hosts = '';
    public $redis_port = '';
    public $redis_password = '';
    public $redis_db = '';

    public $mysql_hosts = '';
    public $mysql_port = '';
    public $mysql_dbName = '';
    public $mysql_userName = '';
    public $mysql_password = '';

    /**
     * 获取连接配置，可动态获取读写配置
     * @param bool $isWrite
     * @param $redis_db
     * @throws Exception
     */
    public function initConfig($isWrite, $redis_db)
    {
        $this->global_config = $this->getConfig();
        if (!extension_loaded('redis')) {
            throw new Exception("REDIS NOT  SUPPORT", 1);
        }

        //TODO:为读写分离预留出来的口子
        if ($isWrite) {
            $config = '';
        } else {
            $config = '';
        }

        $this->redis_hosts = $this->global_config['redis']['host'];
        $this->redis_port = $this->global_config['redis']['port'];
        $this->redis_password = $this->global_config['redis']['auth'];
        $this->redis_db = $redis_db;

        $this->mysql_hosts = '';
        $this->mysql_dbName = '';
        $this->mysql_userName = '';
        $this->mysql_password = '';
        $this->mysql_port = '';
    }

    /**
     * @param bool $isWrite 是否可写
     * @param int $index redis库号
     * @return Redis
     * @throws container_exception_BaseException
     */
    public function getRedis($isWrite, $index)
    {
        $this->initConfig($isWrite, $index);
        return Container_Tool_DataBaseConnection::getRedis($this->redis_hosts, $this->redis_port, $this->redis_password, $this->redis_db);
    }

    /**
     * PDO 连接mysql数据库
     * @param bool $isWrite
     * @param $index
     * @return PDO
     * @throws container_exception_BaseException
     */
    public function getMysql($isWrite, $index)
    {
        $this->initConfig($isWrite, $index);
        return Container_Tool_DataBaseConnection::getPDO($this->mysql_hosts, $this->mysql_dbName, $this->mysql_userName, $this->mysql_password, $this->mysql_port);
    }

    /**
     * Medoo 连接mysql数据库
     * @param bool $isWrite
     * @param $index
     * @return \Medoo\Medoo
     * @throws container_exception_BaseException
     */
    public function getMedoo($isWrite, $index)
    {
        $this->initConfig($isWrite, $index);
        return Container_Tool_DataBaseConnection::getMedoo($this->mysql_hosts, $this->mysql_dbName, $this->mysql_userName, $this->mysql_password, $this->mysql_port);
    }
}