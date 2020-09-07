<?php

/**
 * 类处理
 * Class Container_Tool_ClassInformation
 */
class Container_Tool_ClassInformation
{
    /**
     * 将数组转化为指定的强类列表，不进行数据有效性检查.
     * 只适合简单类，比如数据库表类
     * @param $data
     * @param $className
     * @return array
     * @throws \ReflectionException
     */
    public static function arrayToList($data, $className)
    {
        if (empty($data) || !is_array($data)) {
            return [];
        }

        $resultData = [];
        foreach ($data as $item) {
            $resultData[] = self::arrayToCertainObject($item, $className);
        }
        return $resultData;
    }

    /**
     * 将数组转换为指定的强类，不进行数据有效性检查
     * 只适合简单类，比如数据库表类
     * @param $data
     * @param $className
     * @return object
     * @throws \ReflectionException
     */
    public static function arrayToCertainObject($data, $className)
    {
        $reflect = new \ReflectionClass($className);
        $instance = $reflect->newInstanceWithoutConstructor();

        if (empty($data) || !is_array($data)) {
            return $instance;
        }

        $fieldList = self::getPublicProperties($className);
        foreach ($fieldList as $fieldName) {
            if (isset($data[$fieldName])) {
                $instance->$fieldName = $data[$fieldName];
            }
        }
        return $instance;
    }

    /**
     * 获取类的名称，不包含命名空间
     * @param object|string $instanceClass 类的实例或的类的全名
     * @return string
     */
    public static function getClassNameWithoutNameSpace($instanceClass)
    {
        //$reflect = new \ReflectionObject($this);
        //return $reflect->getShortName();

        if (is_object($instanceClass)) {
            $classNameWithNameSpace = get_class($instanceClass);
        } elseif (is_string($instanceClass)) {
            $classNameWithNameSpace = $instanceClass;
        } else {
            $classNameWithNameSpace = "\\";
        }

        $array = explode('\\', $classNameWithNameSpace);
        return $array[count($array) - 1];
    }

    /**
     * 获取某个类下所有的公共属性
     * @param $instanceClass
     * @return array
     * @throws \Exception
     */
    public static function getPublicProperties($instanceClass)
    {
        try {
            $reflect = new \ReflectionClass($instanceClass);
            $properties = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
        } catch (\ReflectionException $e) {
            return null;
        }

        $fieldNames = [];
        foreach ($properties as $property) {
            $name = $property->getName();
            $fieldNames[] = $name;
        }

        return $fieldNames;
    }

}