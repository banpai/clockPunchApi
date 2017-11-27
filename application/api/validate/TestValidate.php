<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/10/26 0026
 * Time: 0:08
 */

namespace app\api\validate;


use think\Validate;

class TestValidate extends Validate
{
    protected $rule = [
        'name' => 'require|max:10',
        'email' => 'email'
    ];
}