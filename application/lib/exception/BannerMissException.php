<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/10/26 0026
 * Time: 21:51
 */

namespace app\lib\exception;


class BannerMissException extends BaseException
{
    public $code = 200;
    public $msg = '请求Banner不存在';
    public $errorCode = 40000;
}