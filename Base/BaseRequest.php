<?php

/**
 * 请求的基类。
 * Class Container_Base_BaseRequest
 */
class Container_Base_BaseRequest extends Container_StrongType_Base
{

    /**
     * 定义完整的检查规则,应使用assert方法来验证。出错才能抛出异常！
     * @return bool
     */
    public function checkFieldValue()
    {
        return true;
    }

    /**
     * 待检查字段信息在此方法中添加，主要是数组类型
     */
    public function addFieldTypes()
    {
    }

    /**
     * @param $parentClassValue
     * @param string $fieldName
     * @return null
     */
    public function getGenericTypes($parentClassValue, $fieldName = '')
    {
        return null;
    }
}