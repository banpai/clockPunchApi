<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/11/16 0016
 * Time: 16:43
 */

namespace app\api\validate;


class OtherOpenidValidate extends BaseValidate
{
    protected $rule = [
        'otheropenid' => 'require|isPostiveInteger'
    ];
    protected function isPostiveInteger($value, $rule='', $data='', $field=''){
        // 判断正整数
        if($value){
            return true;
        }else{
            return $field.'没有otheropenid';
        }
    }
}