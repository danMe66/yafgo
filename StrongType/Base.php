<?php

use Respect\Validation\Exceptions\NestedValidationException;

/**
 * 数据模型的基类。所有强类型的基类需要检查的字段均应定义为public作用域
 * Class Container_StrongType_Model
 */
abstract class Container_StrongType_Base
{
    protected $AssertProxy;

    private $_errorList;

    public function getError()
    {
        return $this->_errorList;
    }

    //错误Dto说明
    protected $ErrorDto;

    /**
     * @param $errorMessage string 添加错误信息
     */
    public function addError($errorMessage)
    {
        $this->_errorList[] = $errorMessage;
    }

    /**
     * @return array 获取错误信息
     */
    public function getErrors()
    {
        return $this->_errorList;
    }


    /**
     * Model constructor.
     * container_strongType_Model constructor.
     * @param array $data 必须是关联数组
     * @throws container_exception_BaseException
     */
    function __construct($data = [])
    {
        $this->AssertProxy = new Container_Utilities_Assert_AssertProxy();
        $this->ErrorDto = new Container_Error_AssertProxy_ErrorDto();
        $data = json_decode(json_encode($data), true);
        $this->_errorList = [];//参数校验错误列表

        $data = json_decode(json_encode($data), true);
        $this->_fieldNameList = $this->getDefinedFieldNames();//获取接口参数

        if (empty($data)) {
            return;//初始化的时候参数为空，直接返回
        }

        //判断是否是索引数组
        if (container_tool_ArrayHelper::isAssocArray($data)) {
            throw new container_exception_BaseException('不可识别的数据格式');
        }
        $this->addFieldTypes();
        $this->_setValues($data);
        try {
            $this->checkFieldValue();
        } catch (NestedValidationException $exception) {
            throw new container_exception_BaseException($exception->getFullMessage());
        }
    }

    /**
     * 设置各个属性的值
     * @param array $data
     * @throws Exception
     */
    protected function _setValues(array $data)
    {
        $fieldNameList = $this->_fieldNameList;
        $fieldTypeList = $this->_fieldTypeList;

        foreach ($data as $fieldName => $value) {
            // 防止泛型丢失数据;没有自定义检查规则的字段放过
            if (!in_array($fieldName, $fieldNameList) || !array_key_exists($fieldName, $fieldTypeList)) {
                $this->$fieldName = $value;
                continue;
            }

            $fieldInfo = $this->_fieldTypeList[$fieldName];

            /* @var Container_StrongType_FieldTypeInformation $fieldInfo */

            $result = false;
            if (is_callable($fieldInfo->type)) {
                $result = $this->_setCallableValue($fieldName, $fieldInfo, $value);
            } elseif (is_object($fieldInfo->type)) {
                $result = $this->_setObjectValue($fieldName, $fieldInfo, $value);
            } elseif (is_string($fieldInfo->type)) {
                $result = $this->_setObjectValue($fieldName, $fieldInfo, $value);
            }

            if (!$result) {
                $this->addError("$fieldName value is error !");
            }
        }
    }

    /**
     * 这是系统预设属性的处理方案
     * @param $fieldName string
     * @param $fieldInfo Container_StrongType_FieldTypeInformation
     * @param $value
     * @return bool
     */
    private function _setCallableValue($fieldName, $fieldInfo, $value)
    {
        $type = $fieldInfo->type;

        if ($fieldInfo->isArray) {

            if (empty($value)) {
                $this->$fieldName = [];
            }

            if (!is_array($value)) {
                return false;
            }

            $data = [];
            foreach ($value as $item) {
                $result = $type($item);
                if ($result) {
                    $data[] = $value;
                } else {
                    $this->addError("$fieldName value is error !");
                    return false;
                }
            }

            $this->$fieldName = $data;

        } else {
            $result = $type($value);
            if ($result) {
                $this->$fieldName = $value;
            } else {
                $this->addError("$fieldName value is error !");
                return false;
            }
        }

        return true;
    }

    /**
     * 泛型中存对象的情况
     * @param $fieldName
     * @param $fieldInfo
     * @param $value
     * @return bool
     * @throws \Exception
     */
    private function _setObjectValue($fieldName, $fieldInfo, $value)
    {
        /* @var Container_StrongType_FieldTypeInformation $fieldInfo */

        $className = (new \ReflectionClass($fieldInfo->type))->getName();

        if ($fieldInfo->isArray) {
            if (empty($value)) {
                $this->$fieldName = [];
                return true;
            }

            if (!is_array($value)) {
                throw new Container_Exception_BaseException('需要泛型对象', -1);
            }
            $data = [];
            foreach ($value as $item) {
                if ($fieldInfo->isGeneric) {
                    $object = json_decode(json_encode($item));
                    $genericClassName = $this->getGenericTypes($object, $fieldName);

                    if (!empty($genericClassName)) {
                        $className = (new \ReflectionClass($genericClassName))->getName();
                    }
                }
                $object = new $className($item);
                $data[] = $object;
                $this->addChildError($object);
            }
            $this->$fieldName = $data;

        } else {
            $object = json_decode(json_encode($value));
            if (empty($object)) {
                $this->$fieldName = null;
                return true;
            }
            $genericClassName = $this->getGenericTypes($object, $fieldName);
            if (empty($genericClassName)) {
                $object = new $className($value);
                $this->$fieldName = $object;
            } else {
                $object = new $genericClassName($value);
                $this->$fieldName = $object;
            }
            $this->addChildError($object);
        }
        return true;
    }

    /**
     * 将子对象的错误信息全部添加到主对象中
     * @param $childModel Container_StrongType_Base
     */
    private function addChildError($childModel)
    {
        if (empty($childModel) || !$childModel instanceof Container_StrongType_Base) {
            return;
        }
        $errors = $childModel->getErrors();
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->addError($error);
            }
        }
    }

    /**
     * @return bool 定义完整的检查规则，一般不推荐在此方法中添加与数据（库）相关联的检查
     */
    public abstract function checkFieldValue();

    /**
     * @var array FieldTypeInformation 缓存属性检查规则的列表
     */
    protected $_fieldTypeList;

    /**
     * @var array string 属性名称的列表
     */
    protected $_fieldNameList;

    /**
     * 待检查字段信息在此方法中添加，主要是数组类型
     */
    public abstract function addFieldTypes();

    /**
     * 数组类型，主要是可泛型化的数据用此方法检查
     * 除基本类型外的类型定义均用此方法检验
     * @param $fieldName
     * @param $type
     * @param bool $isArray
     * @param bool $isGeneric
     * @throws Exception
     */
    public function addFieldType($fieldName, $type, $isArray = false, $isGeneric = false)
    {
        if (!in_array($fieldName, $this->_fieldNameList)) {
            throw new \Exception();
        }

        // TODO check $type

        if (empty($this->_fieldTypeList)) {
            $this->_fieldTypeList = [];
        }

        $fieldInfo = new Container_StrongType_FieldTypeInformation();
        $fieldInfo->fieldName = $fieldName;
        $fieldInfo->isArray = $isArray;
        $fieldInfo->isGeneric = $isGeneric;
        $fieldInfo->type = $type;

        $this->_fieldTypeList[$fieldName] = $fieldInfo;
    }

    public abstract function getGenericTypes($parentClassValue, $fieldName = '');

    /**
     * @param $parentClassValue
     * @param string $fieldName
     * @return mixed
     * @throws ReflectionException
     */
    public function getGenericType($parentClassValue, $fieldName = '')
    {

        if (empty($fieldName)) {
            $fieldName = $this->getFieldNameByClassType($parentClassValue);
            if (empty($fieldName)) {
                return $parentClassValue;
            }
        }

        return $this->getGenericTypes($parentClassValue, $fieldName);
    }

    /**
     * @param $classValue
     * @param string $fieldName
     * @return mixed
     * @throws ReflectionException
     */
    public function getGenericValue($classValue, $fieldName = '')
    {
        if (empty($fieldName)) {
            $fieldName = $this->getFieldNameByClassType($classValue);
            if (empty($fieldName)) {
                return $classValue;
            }
        }

        $classType = $this->getGenericTypes($classValue, $fieldName);
        if (empty($classType)) {
            return $classValue;
        }

        $reflectionObject = new \ReflectionClass($classType);

        $className = $reflectionObject->getName();

        $classValue = json_encode($classValue);
        $classValue = json_decode($classValue, true);

        $data = new $className($classValue);

        return $data;
    }

    /**
     * 根据给定的值反射出对应（可能）的属性名称
     * TODO:解决不了两个属性有相同的泛型的情况
     * @param $classValue
     * @return string
     * @throws ReflectionException
     */

    private function getFieldNameByClassType($classValue)
    {
        $reflectionObject = new \ReflectionClass($classValue);
        $className = $reflectionObject->getName();

        $fieldTypeList = $this->_fieldTypeList;
        foreach ($fieldTypeList as $name => $info) {
            /* @var Container_StrongType_FieldTypeInformation $info */

            if (is_object($info->type) && $info->isGeneric) {

                $type = new \ReflectionClass($info->type);
                $typeName = $type->getName();

                if ($className == $typeName) {
                    return $info->fieldName;
                }

            }

        }

        return '';
    }

    /**
     * @return array 获取所有的列名
     * @throws Exception
     */
    public function getDefinedFieldNames()
    {
        return container_tool_ClassInformation::getPublicProperties($this);
    }

    /**
     * 获取json序列化后的字符串
     * @return string
     */
    public function getJsonFormatString()
    {
        return json_encode($this);
    }
}