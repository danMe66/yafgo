<?php

/**
 * 日志相关
 * 功能：1：设置日志的级别；2：判断如果大于当前的级别则写入；3：类作为一个单例的对象存在；4:修改类可以自定义日志文件名字
 * Trait Container_Core_Trait_Log
 */
trait Container_Core_Trait_Log
{
    private static $_instance;
    private $fileName;
    private $path;
    private $level;
    private $buffer;
    private $open = '';
    private $config = null;

    //日志添加一个可以自定义文件名字的
    private function __construct($params = array())
    {
        $arrConfig = Yaf_Application::app()->getConfig();
        Yaf_Registry::set('config', $arrConfig);
        $config = new Yaf_Config_Ini(APP_PATH . '/conf/application.ini');
        $this->config = $config->toArray();
        $this->open = $this->config['log']['open'];
        $filerule = $this->config['log']['filerule'];
        $this->path = $this->config['log']['path'];
        if (isset($params['filename']) && $params['filename']) {
            $this->fileName = $params['filename'] . ".log";
        } else {
            if ($filerule == "day") {
                $this->fileName = date("Y-m-d", time()) . ".log";
            } else if ($filerule == "month") {
                $this->fileName = date("Y-m", time()) . ".log";
            } else if ($filerule == "hours") {
                $this->fileName = date("Y-m-d-H", time()) . ".log";
            }
        }
        $this->level = $this->config['log']['level'];
    }

    private function __clone()
    {
    }

    public static function getInstance($params = array())
    {
        if (is_null(self::$_instance) || isset (self::$_instance)) {
            self::$_instance = new self ($params);
        }
        return self::$_instance;

    }

    public function Write($level, $info)
    {
        if ($this->getLevelNumber($level) >= $this->getLevelNumber($this->level)) {
            $this->WriteLog($this->bindLine($level, $info));
        }
    }

    public function WriteWithBuffer($level, $info)
    {
        if ($this->getLevelNumber($level) >= $this->getLevelNumber($this->level)) {
            $this->buffer = $this->buffer . $this->bindLine($level, $info);
        }
    }

    private function bindLine($level, $info)
    {
        return "[" . $level . "] " . date("Y-m-d H:i:s", time()) . "  " . $info . PHP_EOL;
    }

    public function flush()
    {
        $this->WriteLog($this->buffer);
    }

    private function WriteLog($data)
    {
        if (!$this->open) {
            return;
        }
        $path = $this->path . $this->fileName;
        $file = fopen($path, "a+");
        if (flock($file, LOCK_EX)) {
            fwrite($file, $data);
            // release lock
            flock($file, LOCK_UN);
        }
        fclose($file);
    }

    private function getLevelNumber($key)
    {
        $data = ["debug" => 1, "info" => 2, "waring" => 3, "error" => 4];
        if (in_array($key, array_keys($data))) {
            return $data[$key];
        }
        return 0;
    }

    public static function setMLog($logstr)
    {
        if (Yaf_Registry::has("Monitor-log")) {
            $log = Yaf_Registry::get('Monitor-log');
        } else {
            Yaf_Registry::set('Monitor-log', []);
            $log = Yaf_Registry::get('Monitor-log');
        }
        array_push($log, $logstr);
        Yaf_Registry::set('Monitor-log', $log);
    }

    public static function getMLog()
    {
        if (Yaf_Registry::has("Monitor-log")) {
            return Yaf_Registry::get('Monitor-log');
        }
    }

    public static function pointMLog($point_str)
    {
        $curr = ClientFactory::getMillisecond();
        self::setMLog("\n" . $point_str . ":" . $curr);
    }
}

