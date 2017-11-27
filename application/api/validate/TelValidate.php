<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/10/27 0027
 * Time: 9:24
 */

namespace app\api\validate;


class TelValidate extends BaseValidate
{
    protected $rule = [
        'tel' => 'require|isTel'
    ];
    protected function isTel($value, $rule='', $data='', $field=''){
        // 判断正整数
        if(preg_match("/^1[34578]{1}\d{9}$/",$value)){
            return true;
        }else{
            return $field.'必须是手机号';
        }
    }
}