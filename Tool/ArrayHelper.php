<?php

/**
 * 数组的帮助类
 * Class Container_Tool_ArrayHelper
 */
class Container_Tool_ArrayHelper
{
    /**
     * 判断是否为索引数组
     * @param array $array
     * @return bool
     */
    public static function isAssocArray($array)
    {
        if (!is_array($array)) {
            return false;
        }

        // 防止出现数字数组
        $id = 0;
        foreach ($array as $key => $value) {
            if (!is_numeric($key) || $key != $id) {
                return false;
            }
            $id += 1;
        }
        return true;
    }

    /**
     * 类转关联数组（hash形式的）
     * @param object $object
     * @return mixed
     */
    public static function objectToArray($object)
    {
        $object = json_encode($object, JSON_UNESCAPED_UNICODE);
        return json_decode($object, true);
    }

    /**
     * 数组转换为类的定义
     * @param array $array
     * @return mixed
     */
    public static function arrayToObject($array)
    {
        $object = json_encode($array, JSON_UNESCAPED_UNICODE);
        return json_decode($object, true);
    }

    /**
     * 把索引数组转换成按指定键做key的关联数组,没有相应key的元素将被舍弃
     * 主要用做列表转化
     * @param array $array
     * @param $indexName string 对应的键名，值内容应为string，或可以转为string的类型
     * @return array
     */
    public static function indexArrayToRelatedArray(array $array, $indexName)
    {
        $data = [];
        foreach ($array as $key => $value) {
            if (!is_numeric($key)) {
                continue;
            }

            $index = '';
            if (is_array($value) && isset($value[$indexName]) && !empty($value[$indexName])) {
                $index = $value[$indexName];
            } elseif (is_object($value) && isset($value->$indexName) && !empty($value->$indexName)) {
                $index = $value->$indexName;
            }

            // 重复的将被后面覆盖
            if (!empty($index)) {
                $data[$index] = $value;
            }

        }
        return $data;
    }

    /**
     * 获取数组和类的指定索引或是字段的值
     * @param array | object $array
     * @param string | int $indexOrName
     * @param null $defaultValue
     * @return mixed|null
     */
    public static function getValueOrDefault($array, $indexOrName, $defaultValue = null)
    {
        if (is_array($array) && isset($array[$indexOrName])) {
            return $array[$indexOrName];
        } elseif (is_object($array) && isset($array->$indexOrName)) {
            return $array->$indexOrName;
        } else {
            return $defaultValue;
        }
    }


}