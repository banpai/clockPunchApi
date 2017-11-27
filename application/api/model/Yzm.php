<?php
/**
 * Created by PhpStorm.
 * User: 29423
 * Date: 2017/10/27 0027
 * Time: 9:15
 */

namespace app\api\model;

use think\Session;
use app\lib\Qcloud\SmsSingleSender;

class Yzm
{
    public static function getYzm($tel){
        /**
         * 生成验证码
         */
        $code = "";
        for($i=0;$i<6;$i++){
            $code .= rand(0,9);
        }

        /**
         * 发送短信
         */
        $appid = config('msg_appID');
        $appkey = config('msg_appKey');
        $singleSender = new SmsSingleSender($appid, $appkey);
        $params = array($code, "5");
        $result = $singleSender->sendWithParam("86", $tel, 51817, $params, "", "", "");
        $rsp = json_decode($result);
//        var_dump($rsp);

        /**
         * 缓存验证码
         */
        $date = time();
        session::set('yzm', $code);
        session::set('yzm_date', $date);

        return $rsp;
    }
}