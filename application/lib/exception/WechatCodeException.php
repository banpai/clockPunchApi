<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/11/9 0009
 * Time: 10:04
 */

namespace app\lib\exception;


class WechatCodeException extends BaseException
{
    public $code = 200;
    public $msg = '不存在code';
    public $errorCode = 60001;
    public function __construct($url) {
        $this->msg = $url;
    }
}