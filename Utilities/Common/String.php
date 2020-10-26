<?php

class Container_Utilities_Common_String
{
    /**
     * 处理form 提交的参数过滤
     * @param string $string 需要处理的字符串或者数组
     * @param int $force 是否强制进行处理
     * @return array|string
     */
    public static function dAddSlashes($string, $force = 1)
    {
        if (is_array($string)) {
            $keys = array_keys($string);
            foreach ($keys as $key) {
                $val = $string[$key];
                unset($string[$key]);
                $string[addslashes($key)] = self::dAddSlashes($val, $force);
            }
        } else {
            $string = addslashes($string);
        }
        return $string;
    }

    /**
     * 把一个数组或字符串中的字符转化为html实体
     * dhtmlspecialchars实际上是对PHP内置函数htmlspecialchars的二次封装和补充，使得不仅可以处理字符串还可以递归处理数组
     * @param $string
     * @param int $flags
     * @return array|string|string[]|null
     */
    public static function dHtmlSpecialChars($string, $flags = ENT_COMPAT)
    {
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = self::Dhtmlspecialchars($val, $flags);
            }
        } else {
            if ($flags === null) {
                $string = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string);
                if (strpos($string, '&amp;#') !== false) {
                    $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
                }
            } else {
                if (PHP_VERSION < '5.4.0') {
                    $string = htmlspecialchars($string, $flags);
                } else {
                    $charset = 'UTF-8';
                    $string = htmlspecialchars($string, $flags, $charset);
                }
            }
        }
        return $string;
    }

    /**
     * 删除反斜杠
     * 请参考 php 内置方法 stripslashes
     * @param $string
     * @return array|string
     */
    public static function dStripsLashes($string)
    {
        if (empty($string))
            return $string;
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = self::Dstripslashes($val);
            }
        } else {
            $string = stripslashes($string);
        }
        return $string;
    }
}