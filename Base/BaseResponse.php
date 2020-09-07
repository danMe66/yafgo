<?php

class Container_Base_BaseResponse
{
    /**
     * 状态码
     * @var integer
     * @OA\Property()
     */
    public $code;

    /**
     * 提示语
     * @var string
     * @OA\Property()
     */
    public $msg;

    /**
     * 提示语
     * @var object
     * @OA\Property()
     */
    public $data;
}