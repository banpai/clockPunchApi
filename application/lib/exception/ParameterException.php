<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/10/26 0026
 * Time: 23:34
 */

namespace app\lib\exception;


class ParameterException extends BaseException
{
    public $code = 400;
    public $msg = '参数错误';
    public $errorCode = 10000;
}