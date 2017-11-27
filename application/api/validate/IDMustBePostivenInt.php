<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/10/26 0026
 * Time: 0:32
 */

namespace app\api\validate;


class IDMustBePostivenInt extends BaseValidate
{
    protected $rule = [
        'id' => 'require|isPostiveInteger'
    ];

    protected function isPostiveInteger($value, $rule='', $data='', $field=''){
        // 判断正整数
        if(is_numeric($value) && is_int($value + 0) && ($value + 0) > 0){
            return true;
        }else{
            return $field.'必须是正整数';
        }
    }
}