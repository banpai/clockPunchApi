<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/11/14 0014
 * Time: 11:06
 */

namespace app\api\validate;


class OpenidValidate extends BaseValidate
{
    protected $rule = [
        'openid' => 'require|isPostiveInteger'
    ];

    protected function isPostiveInteger($value, $rule='', $data='', $field=''){
        // 判断正整数
        if($value){
            return true;
        }else{
            return $field.'没有openid';
        }
    }
}