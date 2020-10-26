<?php

class Container_Utilities_Common_Time
{
    /**
     * 获取微秒数
     * @return float
     */
    public static function getMillisecond()
    {
        list($msec, $sec) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    }
}