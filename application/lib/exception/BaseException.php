<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/10/26 0026
 * Time: 21:46
 */

namespace app\lib\exception;


use think\Exception;
use Throwable;

class BaseException extends Exception
{
    // HTTP 状态码，404,200
    public $code = 400;

    // 错误信息
    public $msg = '参数错误';

    // 自定义的错误码
    public $errorCode = 10000;

    function __construct($params =[])
    {
        if (!is_array($params)) {
            // throw new Exception('参数必须是数组');
            return;
        }
        if (array_key_exists('code', $params)) {
            $this->code = $params['code'];
        }
        if (array_key_exists('msg', $params)) {
            $this->msg = $params['msg'];
        }
        if (array_key_exists('errorCode', $params)) {
            $this->errorCode = $params['errorCode'];
        }
    }
}