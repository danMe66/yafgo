<?php

use Medoo\Medoo;

/**
 * 数据库连接
 * Class Container_Tool_DataBaseConnection
 */
class Container_Tool_DataBaseConnection
{

    /**
     * 连接Redis数据库
     * @param string $host 连接地址
     * @param int $port 端口
     * @param string $password 登录密码
     * @param int $db 库号
     * @return \Redis
     * @throws container_exception_BaseException
     */
    public static function getRedis($host, $port, $password, $db)
    {
        $redis = new \Redis();

        if (false === $redis->connect($host, $port)) {
            throw new container_exception_BaseException('', -100, "Redis联接失败 host:{$host},port:{$port}");
        }

        if (false === $redis->auth($password)) {
            throw new container_exception_BaseException('', -100, "Redis认证失败 host:{$host},port:{$port}");
        }

        if (false === $redis->select($db)) {
            throw new container_exception_BaseException('', -100, "Redis选择服务器失败 host:{$host},port:{$port},db:{$db}");
        }

        return $redis;
    }


    /**
     * Medoo连接MySQL数据库
     * @param string $host 连接地址
     * @param string $dbName 数据库名称
     * @param string $userName 登录用户
     * @param string $password 登录密码
     * @param int $port 端口（默认：3306）
     * @return medoo
     * @throws container_exception_BaseException
     */
    public static function getMedoo($host, $dbName, $userName, $password, $port = 3306)
    {
        $options = [
            'database_type' => 'mysql',
            'database_name' => $dbName,
            'server' => $host,
            'username' => $userName,
            'password' => $password,
            'charset' => 'utf8',
            'port' => $port
        ];
        try {
            return new medoo($options);
        } catch (\Exception $e) {
            throw new container_exception_BaseException('medoo-mysql 连接失败', -100, $e->getMessage());
        }
    }


    /**
     * PDO 连接MySQL数据库
     * @param string $host 连接地址
     * @param string $dbName 数据库名称
     * @param string $userName 登录用户
     * @param string $password 登录密码
     * @param int $port 端口（默认：3306）
     * @return \PDO
     * @throws BaseException
     * @throws container_exception_BaseException
     */
    public static function getPDO($host, $dbName, $userName, $password, $port = 3306)
    {
        try {
            $dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $dbName . ';charset=utf8';
            $pdo = new \PDO($dsn, $userName, $password);
            $pdo->exec('set names utf8');
            return $pdo;
        } catch (\PDOException $e) {
            throw new container_exception_BaseException('medoo-PDO初始化失败！', -100, $e->getMessage());
        }
    }

}