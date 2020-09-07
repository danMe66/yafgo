<?php

/**
 * 获取redis，mysql实例
 * Trait Container_Handler_HandlerContext
 */
trait Container_Handler_HandlerContext
{
    public $_redis;

    public $redis_hosts = '';
    public $redis_port = '';
    public $redis_password = '';
    public $redis_db = '';

    public $_mysql;

    public $mysql_hosts = '';
    public $mysql_port = '';
    public $mysql_dbName = '';
    public $mysql_userName = '';
    public $mysql_password = '';


    /**
     * handler相关配置
     * @var Container_Handler_HandlerConfig
     */
    public $_config;

    /**
     * 获取连接配置，可动态获取读写配置
     * @param bool $isWrite
     */
    public function _config($isWrite = false)
    {
        if ($isWrite) {
            $config = '';
        } else {
            $config = '';
        }
        $this->redis_hosts = '';
        $this->redis_port = '';
        $this->redis_password = '';
        $this->redis_db = '';

        $this->mysql_hosts = '';
        $this->mysql_dbName = '';
        $this->mysql_userName = '';
        $this->mysql_password = '';
        $this->mysql_port = '';

    }

    /**
     * @param bool $isWrite
     * @return Redis
     * @throws container_exception_BaseException
     */
    public function getRedis($isWrite = false)
    {
        $this->_config($isWrite);
        //判断是否切换数据库为只读实例
        if ($this->_config->_useReadOnlyDb === true && $isWrite === false) {
            return Container_Tool_DataBaseConnection::getRedis($this->redis_hosts, $this->redis_port, $this->redis_password, $this->redis_db);
        }
        return Container_Tool_DataBaseConnection::getRedis($this->redis_hosts, $this->redis_port, $this->redis_password, $this->redis_db);
    }

    /**
     * PDO 连接mysql数据库
     * @param bool $isWrite
     * @return PDO
     * @throws container_exception_BaseException
     */
    public function getMysql($isWrite = false)
    {
        $this->_config($isWrite);
        if ($this->_config->_useReadOnlyDb === true && $isWrite === false) {
            return Container_Tool_DataBaseConnection::getPDO($this->mysql_hosts, $this->mysql_dbName, $this->mysql_userName, $this->mysql_password, $this->mysql_port);
        }
        return Container_Tool_DataBaseConnection::getPDO($this->mysql_hosts, $this->mysql_dbName, $this->mysql_userName, $this->mysql_password, $this->mysql_port);
    }

    /**
     * Medoo 连接mysql数据库
     * @param bool $isWrite
     * @return \Medoo\Medoo
     * @throws container_exception_BaseException
     */
    public function getMedoo($isWrite = false)
    {
        $this->_config($isWrite);
        if ($this->_config->_useReadOnlyDb === true && $isWrite === false) {
            return Container_Tool_DataBaseConnection::getMedoo($this->mysql_hosts, $this->mysql_dbName, $this->mysql_userName, $this->mysql_password, $this->mysql_port);
        }
        return Container_Tool_DataBaseConnection::getMedoo($this->mysql_hosts, $this->mysql_dbName, $this->mysql_userName, $this->mysql_password, $this->mysql_port);
    }
}