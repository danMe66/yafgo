<?php

/**
 * 数据模型类的字段信息
 * Class Container_StrongType_FieldTypeInformation
 */
class Container_StrongType_FieldTypeInformation
{

    /**
     * @var string 字段名称
     */
    public $fieldName;

    /**
     * @var mixed 字段的类型
     */
    public $type;

    /**
     * @var bool 是否为数组类型
     */
    public $isArray;

    /**
     * @var bool 是否做为泛型类型使用
     */
    public $isGeneric;

    /**
     * @var string 属性的注释
     */
    public $comment;

    /**
     * @var bool 是否为set方法存在
     */
    public $isSetFunction = false;

}