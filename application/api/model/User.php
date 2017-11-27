<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/11/7 0007
 * Time: 10:26
 */

namespace app\api\model;
use think\Model;

class User extends Model
{
    /**
     * 更改表
     */
    protected $table = 't_userinfo';
    public static function getByOpenID($openid){
        $user = self::where('openid','=',$openid)->find();
        return $user;
    }
}