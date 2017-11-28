<?php

namespace app\lib\exception;


class ProjectMissException extends BaseException
{
    public $code = 200;
    public $msg = '项目还没有配置ing~';
    public $errorCode = 60002;
}